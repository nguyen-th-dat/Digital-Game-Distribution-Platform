<?php
// =============================================================================
// FINANCE CONTROLLER (admin) - orders, refunds, developer payouts.
// Tables: don_hang, chi_tiet_don_hang, thanh_toan, hoan_tien, so_ke_toan_tac_gia,
// dot_chi_tra, chi_tiet_chi_tra, tai_khoan_nhan_tien_nguoi_dung, quyen_so_huu_tro_choi.
// Money logic lives in OrderModel, RefundModel and RevenueLedgerModel.
// =============================================================================

class FinanceController extends Controller {
    private const PER = 25;

    // ------------------------------------------------------------ orders -----

    public function orders(array $params = []) {
        $this->requireRole('admin');
        $q = mb_substr(trim((string)($_GET['q'] ?? '')), 0, 100);
        $status = isset(labels('order_status')[$_GET['status'] ?? '']) ? (string)$_GET['status'] : '';
        $total = OrderModel::searchCount($q, $status);
        $pages = max(1, (int)ceil($total / self::PER));
        $page = min($this->page(), $pages);
        $orders = OrderModel::search($q, $status, self::PER, ($page - 1) * self::PER);
        foreach ($orders as &$o) {
            $o['items'] = OrderModel::items((int)$o['ma']);
            $o['payments'] = OrderModel::payments((int)$o['ma']);
        }
        unset($o);
        $this->render('admin/orders', [
            'pageTitle' => t('order.title'),
            'filters' => ['q' => $q, 'status' => $status],
            'counts' => array_column(Model::query('SELECT trang_thai, COUNT(*) AS n FROM don_hang GROUP BY trang_thai'), 'n', 'trang_thai'),
            'totals' => Model::one("SELECT COALESCE(SUM(CASE WHEN trang_thai IN ('paid','partially_refunded') THEN tong_thanh_toan END), 0) AS gross,
                (SELECT COALESCE(SUM(so_tien), 0) FROM hoan_tien WHERE trang_thai = 'completed') AS refunded FROM don_hang"),
            'orders' => $orders,
            'total' => $total, 'page' => $page, 'pages' => $pages,
        ]);
    }

    // ----------------------------------------------------------- refunds -----

    public function refunds(array $params = []) {
        $this->requireRole('admin');
        $status = (string)($_GET['status'] ?? 'requested');
        if ($status !== '' && !isset(labels('refund_status')[$status])) {
            $status = 'requested';
        }
        $total = RefundModel::searchCount($status);
        $pages = max(1, (int)ceil($total / self::PER));
        $page = min($this->page(), $pages);
        $this->render('admin/refunds', [
            'pageTitle' => t('nav.refunds'),
            'status' => $status,
            'counts' => array_column(Model::query('SELECT trang_thai, COUNT(*) AS n FROM hoan_tien GROUP BY trang_thai'), 'n', 'trang_thai'),
            'refunds' => RefundModel::search($status, self::PER, ($page - 1) * self::PER),
            'total' => $total, 'page' => $page, 'pages' => $pages,
        ]);
    }

    public function decideRefund(array $params = []) {
        $admin = $this->requireRole('admin');
        $id = (int)($params['id'] ?? 0);
        $back = safe_return((string)($_POST['return_to'] ?? ''), '/admin/refunds');
        $decision = (string)($_POST['quyet_dinh'] ?? '');
        $note = post_str('ghi_chu', 1000);
        if (!in_array($decision, ['approve', 'reject'], true)) {
            $this->fail(t('admin.finance.chon.duyet.hoac.tu.choi'), $back);
        }
        if ($note === null) {
            $this->fail(t('admin.finance.ghi.chu.toi.da.1.000.ky.tu'), $back);
        }
        if ($decision === 'reject' && $note === '') {
            $this->fail(t('admin.finance.hay.ghi.ly.do.tu.choi.nguoi.mua.se.nhan.duoc.ghi.chu.nay'), $back);
        }
        try {
            $result = Model::transaction(function () use ($id, $decision, $note, $admin) {
                $result = RefundModel::decide($id, $decision === 'approve', $note);
                audit('refund_' . $result['decision'], 'hoan_tien', $id, ['ghi_chu' => $note ?: null]);
                if ($result['nguoi_mua_id']) {
                    $approved = $result['decision'] === 'completed';
                    NotificationModel::notifyUsers([(int)$result['nguoi_mua_id']], (int)$admin['ma'], 'refund',($approved ? t('admin.finance.yeu.cau.hoan.tien.da.duoc.chap.nhan') : t('admin.finance.yeu.cau.hoan.tien.bi.tu.choi')),($approved ? ($approved && $note !== '' ? t('admin.finance.da.hoan.cho.quyen.so.huu.muc.nay.da.bi.thu.hoi.ghi.chu', [money($result['so_tien']), $result['tieu_de_luc_mua'], $note]) : t('admin.finance.da.hoan.cho.quyen.so.huu.muc.nay.da.bi.thu.hoi', [money($result['so_tien']), $result['tieu_de_luc_mua']])) : ($approved && $note !== '' ? t('admin.finance.ly.do.ghi.chu', [$note, $note]) : t('download.ly.do', [$note]))),
                        '/orders/' . rawurlencode($result['ma_don_hang']));
                }
                return $result;
            });
        } catch (RuntimeException $error) {
            $this->fail($error->getMessage(), $back);
        }
        flash('success',($result['decision'] === 'completed' ? t('admin.finance.da.hoan.tien.va.thu.hoi.quyen.so.huu') : t('admin.finance.da.tu.choi.yeu.cau.hoan.tien')));
        $this->redirect($back);
    }

    // ----------------------------------------------------------- payouts -----

    public function payouts(array $params = []) {
        $this->requireRole('admin');
        RevenueLedgerModel::releaseDue();
        $status = (string)($_GET['status'] ?? '');
        $where = $status !== '' && isset(labels('payout_status')[$status]) ? 'WHERE p.trang_thai = :s' : "WHERE p.trang_thai IN ('requested','processing')";
        $args = str_contains($where, ':s') ? [':s' => $status] : [];
        if ($args === []) {
            $status = '';
        }
        [$payouts, $pager] = $this->paginate(Model::query("SELECT p.*, u.ten_hien_thi, u.ten_dang_nhap, a.nha_cung_cap, a.ten_tai_khoan, a.ma_tai_khoan_ben_ngoai, a.trang_thai AS tk_trang_thai,
                (SELECT COUNT(*) FROM chi_tiet_chi_tra c WHERE c.dot_chi_tra_id = p.ma) AS so_dong
            FROM dot_chi_tra p JOIN nguoi_dung u ON u.ma = p.nguoi_dung_id JOIN tai_khoan_nhan_tien_nguoi_dung a ON a.ma = p.tai_khoan_nhan_tien_id
            $where ORDER BY p.ngay_yeu_cau DESC, p.ma DESC", $args), self::PER);
        $this->render('admin/payouts', $pager + [
            'pageTitle' => t('admin.payouts.chi_tra_cho_nha_phat'),
            'status' => $status,
            'counts' => array_column(Model::query('SELECT trang_thai, COUNT(*) AS n FROM dot_chi_tra GROUP BY trang_thai'), 'n', 'trang_thai'),
            'payouts' => $payouts,
            'accounts' => Model::query("SELECT a.*, u.ten_hien_thi, u.ten_dang_nhap FROM tai_khoan_nhan_tien_nguoi_dung a JOIN nguoi_dung u ON u.ma = a.nguoi_dung_id
                ORDER BY FIELD(a.trang_thai, 'pending_verification') DESC, a.ngay_tao DESC LIMIT 100"),
            'ledger' => Model::query('SELECT trang_thai, COUNT(*) AS n, SUM(so_tien_thuc_nhan) AS net, SUM(phi_nen_tang) AS fee FROM so_ke_toan_tac_gia GROUP BY trang_thai'),
        ]);
    }

    public function decidePayout(array $params = []) {
        $admin = $this->requireRole('admin');
        $id = (int)($params['id'] ?? 0);
        $status = (string)($_POST['trang_thai'] ?? '');
        $reference = post_str('ma_tham_chieu', 190);
        $reason = post_str('ly_do', 1000);
        if (!in_array($status, ['processing', 'paid', 'failed', 'cancelled'], true)) {
            $this->fail(t('admin.finance.trang.thai.chi.tra.khong.hop.le'), '/admin/payouts');
        }
        if ($reference === null || $reason === null) {
            $this->fail(t('admin.finance.ma.tham.chieu.toi.da.190.ky.tu.ly.do.toi.da.1.000.ky.tu'), '/admin/payouts');
        }
        if ($status === 'paid' && $reference === '') {
            $this->fail(t('admin.finance.nhap.ma.tham.chieu.cua.giao.dich.chuyen.tien.khi.danh.dau.da.chi.tra'), '/admin/payouts');
        }
        if (in_array($status, ['failed', 'cancelled'], true) && $reason === '') {
            $this->fail(t('admin.finance.hay.ghi.ly.do.khi.danh.dau.that.bai.hoac.huy'), '/admin/payouts');
        }
        try {
            RevenueLedgerModel::decidePayout($id, $status, $reference, $reason);
        } catch (RuntimeException $error) {
            $this->fail($error->getMessage(), '/admin/payouts');
        }
        audit('payout_' . $status, 'dot_chi_tra', $id, ['ma_tham_chieu' => $reference ?: null, 'ly_do' => $reason ?: null, 'nguoi_xu_ly' => (int)$admin['ma']]);
        flash('success',t('admin.finance.dot.chi.tra', [$id, label('payout_status', $status)]));
        $this->redirect('/admin/payouts');
    }

    public function decideAccount(array $params = []) {
        $admin = $this->requireRole('admin');
        $id = (int)($params['id'] ?? 0);
        $status = (string)($_POST['trang_thai'] ?? '');
        if (!in_array($status, ['active', 'disabled'], true)) {
            $this->fail(t('admin.finance.trang.thai.tai.khoan.khong.hop.le'), '/admin/payouts#accounts');
        }
        $account = Model::one('SELECT * FROM tai_khoan_nhan_tien_nguoi_dung WHERE ma = :i', [':i' => $id]);
        if (!$account) {
            $this->notFound(t('admin.finance.khong.tim.thay.tai.khoan.nhan.tien'));
        }
        if ($account['trang_thai'] === $status) {
            $this->fail(t('admin.tai.khoan.da.o.trang.thai.nay'), '/admin/payouts#accounts');
        }
        if ($status === 'disabled') {
            $busy = Model::scalar("SELECT 1 FROM dot_chi_tra WHERE tai_khoan_nhan_tien_id = :i AND trang_thai IN ('requested','processing')", [':i' => $id]);
            if ($busy) {
                $this->fail(t('admin.finance.tai.khoan.dang.gan.voi.mot.dot.chi.tra.chua.xong.xu.ly.dot.chi.tra.truoc'), '/admin/payouts#accounts');
            }
        }
        Model::transaction(function () use ($id, $status, $account, $admin) {
            Model::run('UPDATE tai_khoan_nhan_tien_nguoi_dung SET trang_thai = :s WHERE ma = :i', [':s' => $status, ':i' => $id]);
            audit('payout_account_' . $status, 'tai_khoan_nhan_tien_nguoi_dung', $id);
            NotificationModel::notifyUsers([(int)$account['nguoi_dung_id']], (int)$admin['ma'], 'payout',($status === 'active' ? t('admin.finance.tai.khoan.nhan.tien.da.duoc.xac.minh') : t('admin.finance.tai.khoan.nhan.tien.da.bi.vo.hieu.hoa')),($account['ten_tai_khoan'] ? t('devlog.message', [$account['nha_cung_cap'], $account['ten_tai_khoan']]) : t('devlog.message', [$account['nha_cung_cap'], $account['ma_tai_khoan_ben_ngoai']])), '/dev/payouts');
        });
        flash('success',t('admin.finance.da.cap.nhat.tai.khoan.nhan.tien'));
        $this->redirect('/admin/payouts#accounts');
    }
}
