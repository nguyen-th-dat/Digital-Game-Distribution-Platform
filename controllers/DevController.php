<?php
// =============================================================================
// DEV CONTROLLER - developer overview, sales ledger and payouts.
// Tables: tro_choi, tac_gia_tro_choi, thong_ke_tro_choi_hang_ngay, so_ke_toan_tac_gia,
// tai_khoan_nhan_tien_nguoi_dung, dot_chi_tra, chi_tiet_chi_tra.
// Money logic lives in RevenueLedgerModel; this controller only validates and routes.
// =============================================================================

class DevController extends Controller {
    private const PER = 25;
    private const MAX_ACCOUNTS = 5;

    public function overview(array $params = []) {
        $user = $this->requireRole('developer');
        $uid = (int)$user['ma'];
        $games = GameModel::listForAuthor($uid);
        $series = DailyStatsModel::series(array_map('intval', array_column($games, 'ma')), 30);
        $sum = static fn(string $c): float => array_sum(array_column($series, $c));
        $this->render('dev/overview', [
            'pageTitle' => t('dev.overview.tong_quan_nha_phat_trien'),
            'balances' => RevenueLedgerModel::balances($uid),
            'games' => $games,
            'traffic' => ['views' => $sum('luot_xem_trang'), 'downloads' => $sum('luot_nhan_tai_xuong'), 'purchases' => $sum('so_luot_mua'), 'revenue' => $sum('doanh_thu_gop')],
            'recent' => RevenueLedgerModel::rowsFor($uid, '', 6, 0),
            'hasAccount' => (bool)Model::scalar("SELECT 1 FROM tai_khoan_nhan_tien_nguoi_dung WHERE nguoi_dung_id = :u AND trang_thai = 'active' LIMIT 1", [':u' => $uid]),
        ]);
    }

    public function sales(array $params = []) {
        $user = $this->requireRole('developer');
        $uid = (int)$user['ma'];
        $balances = RevenueLedgerModel::balances($uid);
        $status = (string)($_GET['status'] ?? '');
        if (!isset(labels('ledger_status')[$status])) {
            $status = '';
        }
        $total = RevenueLedgerModel::countFor($uid, $status);
        $pages = max(1, (int)ceil($total / self::PER));
        $page = min($this->page(), $pages);
        $this->render('dev/sales', [
            'pageTitle' => 'Doanh thu',
            'balances' => $balances,
            'status' => $status,
            'counts' => array_column(Model::query('SELECT trang_thai, COUNT(*) n FROM so_ke_toan_tac_gia WHERE nguoi_dung_id = :u GROUP BY trang_thai', [':u' => $uid]), 'n', 'trang_thai'),
            'byGame' => RevenueLedgerModel::salesByGame($uid),
            'rows' => RevenueLedgerModel::rowsFor($uid, $status, self::PER, ($page - 1) * self::PER),
            'total' => $total, 'page' => $page, 'pages' => $pages,
        ]);
    }

    public function payouts(array $params = []) {
        $user = $this->requireRole('developer');
        $uid = (int)$user['ma'];
        $this->render('dev/payouts', [
            'pageTitle' => t('nav.payouts'),
            'balances' => RevenueLedgerModel::balances($uid),
            'accounts' => RevenueLedgerModel::accounts($uid),
            'payouts' => RevenueLedgerModel::payouts($uid),
            'openPayout' => (bool)Model::scalar("SELECT 1 FROM dot_chi_tra WHERE nguoi_dung_id = :u AND trang_thai IN ('requested','processing') LIMIT 1", [':u' => $uid]),
            'maxAccounts' => self::MAX_ACCOUNTS,
        ]);
    }

    public function addPayoutAccount(array $params = []) {
        $user = $this->requireRole('developer');
        $uid = (int)$user['ma'];
        $back = '/dev/payouts#add-account';
        $provider = (string)($_POST['nha_cung_cap'] ?? '');
        $external = post_str('ma_tai_khoan_ben_ngoai', 255);
        $name = post_str('ten_tai_khoan', 120);

        if (!isset(labels('payout_provider')[$provider])) {
            $this->fail(t('dev.chon.phuong.thuc.nhan.tien'), $back);
        }
        if ($name === null || $name === '') {
            $this->fail(t('dev.nhap.ten.chu.tai.khoan.toi.da.120.ky.tu'), $back);
        }
        $external = preg_replace('/\s+/', $provider === 'bank_transfer' ? '' : ' ', trim((string)$external));
        $valid = match ($provider) {
            'bank_transfer' => (bool)preg_match('/^[0-9]{6,34}$/', $external),
            'paypal' => filter_var($external, FILTER_VALIDATE_EMAIL) !== false && mb_strlen($external) <= 255,
            'stripe' => (bool)preg_match('/^acct_[A-Za-z0-9]{6,60}$/', $external),
        };
        if (!$valid) {
            $this->fail(match ($provider) {
                'bank_transfer' => t('dev.so.tai.khoan.ngan.hang.gom.6.den.34.chu.so'),
                'paypal' => t('dev.nhap.email.paypal.hop.le'),
                'stripe' => t('dev.ma.tai.khoan.stripe.connect.co.dang.acct.theo.sau.la.chu.va.so'),
            }, $back);
        }
        $existing = RevenueLedgerModel::accounts($uid);
        if (count($existing) >= self::MAX_ACCOUNTS) {
            $this->fail(t('dev.moi.tai.khoan.chi.duoc.luu.toi.da.phuong.thuc.nhan.tien', [self::MAX_ACCOUNTS]), $back);
        }
        foreach ($existing as $a) {
            if ($a['nha_cung_cap'] === $provider && $a['ma_tai_khoan_ben_ngoai'] === $external) {
                $this->fail(t('dev.phuong.thuc.nhan.tien.nay.da.co.trong.danh.sach'), $back);
            }
        }
        Model::transaction(function () use ($uid, $provider, $external, $name, $existing, $user) {
            $id = PayoutAccountModel::create($uid, $provider, $external, $name, APP_CURRENCY, !$existing);
            audit('payout_account_added', 'tai_khoan_nhan_tien_nguoi_dung', $id, ['nha_cung_cap' => $provider]);
            NotificationModel::notifyUsers(self::adminIds(), $uid, 'payout',t('dev.tai.khoan.nhan.tien.moi.can.xac.minh'),t('dev.da.them', [$user['ten_hien_thi'], label('payout_provider', $provider)]), '/admin/payouts#accounts');
        });
        flash('success',t('dev.da.them.phuong.thuc.nhan.tien.quan.tri.vien.se.xac.minh.truoc.khi.ban.co.the.rut.tien'));
        $this->redirect('/dev/payouts');
    }

    public function defaultPayoutAccount(array $params = []) {
        $user = $this->requireRole('developer');
        $uid = (int)$user['ma'];
        $id = (int)($params['id'] ?? 0);
        $account = Model::one('SELECT * FROM tai_khoan_nhan_tien_nguoi_dung WHERE ma = :i AND nguoi_dung_id = :u', [':i' => $id, ':u' => $uid]);
        if (!$account) {
            $this->notFound(t('dev.khong.tim.thay.phuong.thuc.nhan.tien'));
        }
        if ($account['trang_thai'] === 'disabled') {
            $this->fail(t('dev.khong.the.chon.phuong.thuc.da.bi.vo.hieu.hoa.lam.mac.dinh'), '/dev/payouts');
        }
        Model::transaction(function () use ($uid, $id) {
            Model::run('UPDATE tai_khoan_nhan_tien_nguoi_dung SET mac_dinh = (ma = :i) WHERE nguoi_dung_id = :u', [':i' => $id, ':u' => $uid]);
            audit('payout_account_default', 'tai_khoan_nhan_tien_nguoi_dung', $id);
        });
        flash('success',t('dev.da.dat.lam.phuong.thuc.mac.dinh'));
        $this->redirect('/dev/payouts');
    }

    public function requestPayout(array $params = []) {
        $user = $this->requireRole('developer');
        $uid = (int)$user['ma'];
        try {
            $payoutId = Model::transaction(function () use ($uid, $user) {
                $id = RevenueLedgerModel::requestPayout($uid);
                $amount = (float)Model::scalar('SELECT so_tien FROM dot_chi_tra WHERE ma = :i', [':i' => $id]);
                audit('payout_requested', 'dot_chi_tra', $id, ['so_tien' => $amount]);
                NotificationModel::notifyUsers(self::adminIds(), $uid, 'payout',t('dev.yeu.cau.chi.tra.moi'),t('dev.yeu.cau.rut', [$user['ten_hien_thi'], money($amount)]), '/admin/payouts');
                return $id;
            });
        } catch (RuntimeException $error) {
            $this->fail($error->getMessage(), '/dev/payouts');
        }
        flash('success',t('dev.da.gui.yeu.cau.chi.tra', [$payoutId]));
        $this->redirect('/dev/payouts');
    }

    /** Active administrators, who verify payout accounts and pay out. */
    private static function adminIds(): array {
        return array_map('intval', Model::column("SELECT r.nguoi_dung_id FROM nguoi_dung_vai_tro r JOIN vai_tro v ON v.ma = r.vai_tro_id
            JOIN nguoi_dung u ON u.ma = r.nguoi_dung_id WHERE v.ma_vai_tro = 'admin' AND u.trang_thai = 'active'"));
    }
}
