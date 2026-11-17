<?php
// =============================================================================
// ORDER CONTROLLER - Tables: don_hang, chi_tiet_don_hang, thanh_toan, hoan_tien
// Buyers see their own orders; staff (admin) can open any order.
// =============================================================================

class OrderController extends Controller {

    public function index(array $params = []) {
        $user = $this->requireAuth();
        $status = (string)($_GET['status'] ?? '');
        if ($status !== '' && !array_key_exists($status, labels('order_status'))) {
            $status = '';
        }
        [$orders, $pager] = $this->paginate(OrderModel::listForUser((int)$user['ma'], $status), 20);
        $this->render('order/index', $pager + [
            'pageTitle' => t('order.title'),
            'orders' => $orders,
            'status' => $status,
        ]);
    }

    public function show(array $params = []) {
        $user = $this->requireAuth();
        $order = $this->ownOrder($params, $user);
        $items = OrderModel::items((int)$order['ma']);
        $mine = (int)$order['nguoi_mua_id'] === (int)$user['ma'];
        foreach ($items as &$item) {
            $item['refund_problem'] = $mine ? RefundModel::problem($order, $item) : t('order.chi.nguoi.mua.moi.co.the.yeu.cau.hoan.tien');
        }
        unset($item);
        $this->render('order/show', [
            'pageTitle' => t('order.don.hang', [$order['ma_don_hang']]),
            'order' => $order,
            'items' => $items,
            'payments' => OrderModel::payments((int)$order['ma']),
            'refunds' => OrderModel::refunds((int)$order['ma']),
            'coupon' => OrderModel::couponUse((int)$order['ma']),
            'mine' => $mine,
        ]);
    }

    public function requestRefund(array $params = []) {
        $user = $this->requireAuth();
        $order = $this->ownOrder($params, $user);
        $to = '/orders/' . rawurlencode($order['ma_don_hang']);
        if ((int)$order['nguoi_mua_id'] !== (int)$user['ma']) {
            $this->forbidden(t('order.chi.nguoi.mua.moi.co.the.yeu.cau.hoan.tien'));
        }
        $itemId = (int)($_POST['item_id'] ?? 0);
        $item = null;
        foreach (OrderModel::items((int)$order['ma']) as $row) {
            if ((int)$row['ma'] === $itemId) {
                $item = $row;
            }
        }
        if (!$item) {
            $this->fail(t('order.muc.can.hoan.tien.khong.thuoc.don.hang.nay'), $to);
        }
        if ($problem = RefundModel::problem($order, $item)) {
            $this->fail($problem, $to);
        }
        $reason = trim((string)($_POST['ly_do'] ?? ''));
        if (mb_strlen($reason) < 10 || mb_strlen($reason) > 2000) {
            $this->fail(t('order.ly.do.hoan.tien.can.tu.10.den.2.000.ky.tu'), $to . '#item-' . $itemId);
        }
        try {
            $refundId = RefundModel::request($order, $item, (int)$user['ma'], $reason);
        } catch (RuntimeException $error) {
            $this->fail($error->getMessage(), $to);
        }
        audit('refund_request', 'hoan_tien', $refundId);
        $admins = Model::column("SELECT DISTINCT x.nguoi_dung_id FROM nguoi_dung_vai_tro x JOIN vai_tro r ON r.ma = x.vai_tro_id WHERE r.ma_vai_tro = 'admin'");
        NotificationModel::notifyUsers($admins, (int)$user['ma'], 'refund',t('order.yeu.cau.hoan.tien.moi'),t('order.message', [$order['ma_don_hang'], $item['tieu_de_luc_mua'], money((float)$item['thanh_tien_dong'])]), '/admin/refunds?status=requested');
        flash('success',t('order.da.gui.yeu.cau.hoan.tien.quan.tri.vien.se.xem.xet.va.thong.bao.cho.ban'));
        $this->redirect($to);
    }

    private function ownOrder(array $params, array $user): array {
        $order = OrderModel::findByCode((string)($params['code'] ?? ''));
        if (!$order || ((int)$order['nguoi_mua_id'] !== (int)$user['ma'] && !has_role('admin'))) {
            $this->notFound(t('order.khong.tim.thay.don.hang.nay'));
        }
        return $order;
    }
}
