<?php
// =============================================================================
// CART CONTROLLER - Tables: gio_hang, muc_gio_hang (checkout lives in CartModel)
// Payment is a sandbox: no real money moves and every order is marked TEST-.
// =============================================================================

class CartController extends Controller {
    private const PWYW_MAX = 1000;

    public function index(array $params = []) {
        $user = $this->requireAuth();
        $lines = CartModel::lines((int)$user['ma']);
        $coupon = CartModel::appliedCoupon();
        $totals = CartModel::totals($lines, $coupon);
        $this->render('cart/index', [
            'pageTitle' => t('nav.cart'),
            'lines' => $lines,
            'coupon' => $coupon,
            'totals' => $totals,
        ]);
    }

    public function add(array $params = []) {
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $type = (string)($_POST['type'] ?? '');
        $id = (int)($_POST['id'] ?? 0);

        if ($type === 'game' && isset($_POST['amount'])) {
            $game = GameModel::findWithCard($id);
            if ($game && $game['mo_hinh_gia'] === 'pay_what_you_want') {
                $amount = parse_money($_POST['amount']);
                $min = (float)$game['gia_toi_thieu'];
                if ($amount === null || $amount < $min || $amount > self::PWYW_MAX) {
                    flash('error',t('cart.so.tien.phai.tu.den', [money($min), money(self::PWYW_MAX)]));
                    $this->back('/cart');
                }
                $_SESSION['pwyw'][$id] = $amount;
            }
        }

        $fromCart = ($_POST['from'] ?? '') === 'cart';
        $error = CartModel::add($uid, $type, $id);
        $already = CartModel::already();
        if ($error === $already && !$fromCart) {
            flash('info', $error);
            $this->back('/cart');
        }
        if ($error && $error !== $already) {
            flash('error', $error);
            $this->back('/cart');
        }
        if ($fromCart) {
            flash('success',t('cart.da.cap.nhat.gio.hang'));
            $this->redirect('/cart');
        }
        flash('success',t('cart.item_added'));
        $this->back('/cart');
    }

    public function remove(array $params = []) {
        $user = $this->requireAuth();
        $itemId = (int)($params['id'] ?? 0);
        $gameId = (int)Model::scalar('SELECT i.tro_choi_id FROM muc_gio_hang i JOIN gio_hang c ON c.ma = i.gio_hang_id WHERE i.ma = :i AND c.nguoi_dung_id = :u', [':i' => $itemId, ':u' => (int)$user['ma']]);
        if (CartModel::remove((int)$user['ma'], $itemId)) {
            if ($gameId) {
                unset($_SESSION['pwyw'][$gameId]);
            }
            flash('success',t('cart.da.bo.san.pham.khoi.gio.hang'));
        }
        $this->redirect('/cart');
    }

    public function applyCoupon(array $params = []) {
        $user = $this->requireAuth();
        $code = strtoupper(trim((string)($_POST['code'] ?? '')));
        if (!preg_match('/^[A-Z0-9_-]{4,40}$/', $code)) {
            $this->fail(t('cart.ma.giam.gia.gom.4.40.ky.tu.chu.so.gach.ngang.hoac.gach.duoi'), '/cart');
        }
        $coupon = CouponModel::findByCode($code);
        if (!$coupon) {
            $this->fail(t('cart.khong.tim.thay.ma.giam.gia.nay'), '/cart');
        }
        if ($problem = CouponModel::problem($coupon)) {
            $this->fail($problem, '/cart');
        }
        $lines = CartModel::lines((int)$user['ma']);
        $totals = CartModel::totals($lines, $coupon);
        if (!$totals['coupon_applies']) {
            $this->fail(t('cart.ma.nay.khong.ap.dung.cho.san.pham.nao.trong.gio.hang'), '/cart');
        }
        $_SESSION['cart_coupon'] = $code;
        flash('success',t('cart.da.ap.dung.ma.giam', [$code, money($totals['coupon'])]));
        $this->redirect('/cart');
    }

    public function removeCoupon(array $params = []) {
        $this->requireAuth();
        unset($_SESSION['cart_coupon']);
        flash('info',t('cart.da.go.ma.giam.gia'));
        $this->redirect('/cart');
    }

    public function checkout(array $params = []) {
        $user = $this->requireAuth();
        if (empty($_POST['confirm_sandbox'])) {
            $this->fail(t('cart.hay.xac.nhan.ban.hieu.day.la.thanh.toan.thu.nghiem'), '/cart');
        }
        try {
            $code = CartModel::checkout((int)$user['ma']);
        } catch (RuntimeException $error) {
            $this->fail($error->getMessage(), '/cart');
        }
        audit('order_paid', 'don_hang', (int)Model::scalar('SELECT ma FROM don_hang WHERE ma_don_hang = :c', [':c' => $code]));
        flash('success',t('cart.thanh.toan.thu.nghiem.thanh.cong.tro.choi.da.duoc.them.vao.thu.vien'));
        $this->redirect('/orders/' . rawurlencode($code));
    }
}
