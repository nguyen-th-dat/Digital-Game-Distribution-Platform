<?php
// =============================================================================
// PASSWORD RESET CONTROLLER - quên mật khẩu bằng mã xác nhận gửi qua email.
//
// Luồng:
//   1. Người dùng nhập email và bấm "Kiểm tra" -> hệ thống tìm tài khoản và
//      hiện thông tin (tên đăng nhập, ảnh đại diện) để người dùng xác nhận đúng
//      tài khoản của mình.
//   2. Người dùng đồng ý tiếp tục -> hệ thống sinh mã 6 chữ số, lưu dạng băm
//      kèm thời hạn và gửi mã về email.
//   3. Người dùng nhập mã + mật khẩu mới -> mật khẩu được đổi, mã bị vô hiệu.
//
// Mã xác nhận chỉ dùng được trong 10 phút và tối đa 5 lần nhập sai.
// =============================================================================

class PasswordResetController extends Controller {
    /** Số lần yêu cầu gửi mã cho cùng một email trong khung thời gian. */
    private const MAX_REQUESTS = 5;
    private const WINDOW_MINUTES = 15;
    /** Thời gian chờ tối thiểu giữa hai lần gửi mã cho cùng một tài khoản (giây). */
    private const RESEND_COOLDOWN = 60;

    public function showForgotPassword(array $params = []) {
        if ($this->currentUser()) {
            $this->redirect('/library');
        }
        $this->render('auth/forgot-password', ['pageTitle' => t('auth.forgot_title')]);
    }

    /** Kiểm tra email có tồn tại và hiện thông tin tài khoản để người dùng đồng ý. */
    public function checkEmail(array $params = []) {
        $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 254) {
            $this->fail(t('auth.email.khong.hop.le'), '/auth/forgot-password');
        }

        $user = UserModel::findBy('email', $email);
        if (!$user) {
            $this->fail(t('auth.email.nay.khong.co.trong.he.thong'), '/auth/forgot-password');
        }
        if ($user['trang_thai'] !== 'active') {
            $this->fail(t('auth.tai.khoan.nay.khong.con.hoat.dong.vui.long.lien.he.quan.tri.vien'), '/auth/forgot-password');
        }

        audit('password_reset_email_checked', 'nguoi_dung', (int)$user['ma'], ['email' => $email]);
        $this->render('auth/forgot-password-account', [
            'pageTitle' => t('auth.forgot-password-account.kiem_tra_tai_khoan'),
            'user' => $user,
            'email' => $email,
        ]);
    }

    /** Sinh mã xác nhận và gửi đi (sau khi người dùng đã đồng ý ở bước kiểm tra). */
    public function sendResetCode(array $params = []) {
        $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 254) {
            $this->fail(t('auth.email.khong.hop.le'), '/auth/forgot-password');
        }

        // Chặn ngay khi kiểm tra email: lần bấm lại trong thời gian chờ chỉ trả
        // lại kết quả lần trước, không kiểm tra email và không gửi mail lần nữa.
        $sent = $this->lastSentResult($email);
        if ($sent !== null) {
            flash('success', $sent);
            $this->redirect('/auth/reset-password?email=' . rawurlencode($email));
        }

        $user = UserModel::findBy('email', $email);
        if (!$user) {
            $this->fail(t('auth.email.nay.khong.co.trong.he.thong'), '/auth/forgot-password');
        }
        if ($user['trang_thai'] !== 'active') {
            $this->fail(t('auth.tai.khoan.nay.khong.con.hoat.dong.vui.long.lien.he.quan.tri.vien'), '/auth/forgot-password');
        }

        $userId = (int)$user['ma'];

        // Đã yêu cầu gửi mã gần đây ở phiên khác: chặn gửi lại, chỉ trả kết quả.
        $wait = $this->cooldownRemaining($userId);
        if ($wait > 0) {
            audit('password_reset_resend_blocked', 'nguoi_dung', $userId, ['email' => $email, 'wait' => $wait]);
            $message = t('auth.ma.xac.nhan.da.duoc.gui.toi.vui.long.doi.giay.nua.roi.gui.lai', [$email, $wait]);
            $this->rememberSentResult($email, $message);
            flash('success', $message);
            $this->redirect('/auth/reset-password?email=' . rawurlencode($email));
        }

        if (!Mailer::configured()) {
            $this->fail(t('auth.he.thong.chua.cau.hinh.gui.email.vui.long.lien.he.quan.tri.vien'), '/auth/forgot-password');
        }

        if ($this->tooManyRequests($email)) {
            $this->fail(t('auth.ban.da.yeu.cau.ma.xac.nhan.qua.nhieu.lan.hay.thu.lai.sau.phut', [self::WINDOW_MINUTES]), '/auth/forgot-password');
        }

        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = (new DateTime('now', new DateTimeZone(APP_TIMEZONE)))
            ->modify('+' . PasswordResetModel::TTL_MINUTES . ' minutes')
            ->format('Y-m-d H:i:s');
        PasswordResetModel::issue($userId, $email, $code, $expiresAt);

        $delivered = Mailer::send(
            $email,
            t('password-reset.ma.xac.nhan.dat.lai.mat.khau'),
            $this->mailBody($user, $code, $expiresAt)
        );
        audit('password_reset_requested', 'nguoi_dung', $userId, ['email' => $email, 'sent' => $delivered]);

        if (!$delivered) {
            PasswordResetModel::invalidateAll($userId);
            $this->fail(t('auth.khong.gui.duoc.email.vui.long.kiem.tra.cau.hinh.smtp.hoac.thu.lai.sau'), '/auth/forgot-password');
        }

        $message = t('auth.ma.xac.nhan.da.duoc.gui.toi.vui.long.kiem.tra.hop.thu.cua.ban', [$email]);
        $this->rememberSentResult($email, $message);
        flash('success', $message);
        $this->redirect('/auth/reset-password?email=' . rawurlencode($email));
    }

    public function showResetPassword(array $params = []) {
        if ($this->currentUser()) {
            $this->redirect('/library');
        }
        $email = mb_strtolower(trim((string)($_GET['email'] ?? '')));
        $user = $email !== '' ? UserModel::findBy('email', $email) : null;
        $this->render('auth/reset-password', [
            'pageTitle' => t('auth.reset_title'),
            'email' => $email,
            'resendWait' => $user ? $this->cooldownRemaining((int)$user['ma']) : 0,
        ]);
    }

    /** Kiểm tra mã xác nhận và đổi mật khẩu. */
    public function resetPassword(array $params = []) {
        $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
        $code = trim((string)($_POST['code'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 254) {
            $this->fail(t('auth.email.khong.hop.le'), '/auth/reset-password');
        }
        if (!preg_match('/^\d{6}$/', $code)) {
            $this->fail(t('auth.ma.xac.nhan.gom.6.chu.so'), '/auth/reset-password');
        }
        if (strlen($password) < 8 || strlen($password) > 72) {
            $this->fail(t('auth.mat.khau.moi.can.8.72.ky.tu'), '/auth/reset-password');
        }
        if (!hash_equals($password, $confirm)) {
            $this->fail(t('auth.hai.lan.nhap.mat.khau.moi.khong.khop'), '/auth/reset-password');
        }

        $user = UserModel::findBy('email', $email);
        $candidates = $user ? PasswordResetModel::pendingForEmail((int)$user['ma'], $email) : [];
        $matched = null;
        foreach ($candidates as $row) {
            if (password_verify($code, $row['ma_bam'])) {
                $matched = $row;
                break;
            }
        }
        if (!$matched) {
            foreach ($candidates as $row) {
                PasswordResetModel::registerFailure((int)$row['ma']);
            }
            audit('password_reset_failed', 'nguoi_dung', $user ? (int)$user['ma'] : null, ['email' => $email]);
            $this->fail(t('auth.ma.xac.nhan.khong.dung.hoac.da.het.han'), '/auth/reset-password');
        }

        $userId = (int)$user['ma'];
        PasswordResetModel::markUsed((int)$matched['ma']);
        UserModel::update($userId, ['mat_khau_da_bam' => password_hash($password, PASSWORD_DEFAULT)]);
        // Các mã khác của tài khoản cũng không dùng lại được.
        PasswordResetModel::invalidateAll($userId);
        audit('password_reset', 'nguoi_dung', $userId, ['email' => $email]);
        NotificationModel::create($userId, 'account',t('auth.mat.khau.da.duoc.dat.lai'),t('auth.mat.khau.cua.ban.vua.duoc.dat.lai.bang.ma.xac.nhan.neu.khong.phai.ban.hay.lien.he.quan.tri.vien.ngay'), '/settings#password');

        flash('success',t('auth.dat.lai.mat.khau.thanh.cong.vui.long.dang.nhap.voi.mat.khau.moi'));
        $this->redirect('/auth/login');
    }

    /**
     * Kết quả gửi mã đã lưu trong phiên cho email này, hoặc null nếu phiên
     * chưa gửi mã nào (hoặc đã quá thời gian chờ).
     */
    private function lastSentResult(string $email): ?string {
        $stored = $_SESSION['password_reset_sent'][$email] ?? null;
        if (!is_array($stored) || !isset($stored['message'], $stored['at'])) {
            return null;
        }
        if (time() - (int)$stored['at'] > self::RESEND_COOLDOWN) {
            return null;
        }
        return (string)$stored['message'];
    }

    /** Nhớ kết quả gửi mã để lần bấm lại chỉ trả lại kết quả này. */
    private function rememberSentResult(string $email, string $message): void {
        $_SESSION['password_reset_sent'][$email] = ['message' => $message, 'at' => time()];
    }

    /** Số giây còn phải chờ trước khi được gửi mã mới (0 nếu đã hết chờ). */
    private function cooldownRemaining(int $userId): int {
        $last = PasswordResetModel::lastRequestedAt($userId);
        if ($last === null) {
            return 0;
        }
        $wait = self::RESEND_COOLDOWN - (time() - $last);
        return $wait > 0 ? $wait : 0;
    }

    /** Số lần yêu cầu gửi mã cho email này trong khung thời gian. */
    private function tooManyRequests(string $email): bool {
        $sql = "SELECT COUNT(*) FROM nhat_ky_he_thong
                WHERE ma_hanh_dong = 'password_reset_requested'
                  AND ngay_tao >= NOW() - INTERVAL " . self::WINDOW_MINUTES . " MINUTE
                  AND du_lieu_bo_sung LIKE :email ESCAPE '!'";
        $pattern = '%"email":"' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $email) . '"%';
        return (int)Model::scalar($sql, [':email' => $pattern]) >= self::MAX_REQUESTS;
    }

    /** Nội dung email chứa mã xác nhận. */
    private function mailBody(array $user, string $code, string $expiresAt): string {
        $view = new View();
        return $view->render('mail/reset-code', [
            'user' => $user,
            'code' => $code,
            'expiresAt' => $expiresAt,
            'minutes' => PasswordResetModel::TTL_MINUTES,
            'resetUrl' => app_url('/auth/reset-password?email=' . rawurlencode((string)$user['email'])),
        ], null);
    }
}
