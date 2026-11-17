<?php
// =============================================================================
// SETTINGS CONTROLLER - Tables: nguoi_dung, cai_dat_nguoi_dung, nguoi_dung_vai_tro
// One page with four forms: public profile, password, privacy/preferences and
// the opt-in to the developer role. CSRF is checked centrally in public/index.php.
// =============================================================================

class SettingsController extends Controller {
    private const AVATAR_MAX = 2 * 1024 * 1024;
    private const COVER_MAX = 5 * 1024 * 1024;

    public function index(array $params = []) {
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $this->render('settings/index', [
            'pageTitle' => t('settings.title'),
            'me' => UserModel::find($uid),
            'settings' => UserModel::settings($uid),
            'isDeveloper' => has_role('developer'),
        ]);
    }

    public function legacy(array $params = []) {
        $this->redirect('/settings');
    }

    public function profile(array $params = []) {
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $me = UserModel::find($uid);

        $name = post_str('ten_hien_thi', 100);
        $bio = post_str('gioi_thieu', 2000);
        $site = post_str('url_website', 500);
        $country = strtoupper((string)post_str('ma_quoc_gia', 2));
        $errors = [];
        if ($name === null || $name === '') {
            $errors[] = t('settings.ten.hien.thi.can.1.100.ky.tu');
        }
        if ($bio === null) {
            $errors[] = t('settings.gioi.thieu.toi.da.2000.ky.tu');
        }
        if ($site === null || ($site !== '' && (!filter_var($site, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $site)))) {
            $errors[] = t('settings.website.phai.la.dia.chi.http.hoac.https.hop.le.toi.da.500.ky.tu');
        }
        if ($country !== '' && !preg_match('/^[A-Z]{2}$/', $country)) {
            $errors[] = t('settings.ma.quoc.gia.gom.2.chu.cai.vi.du.vn');
        }
        if ($errors) {
            $this->fail(implode(' ', $errors), '/settings#profile');
        }

        $fields = [
            'ten_hien_thi' => $name,
            'gioi_thieu' => $bio !== '' ? $bio : null,
            'url_website' => $site !== '' ? $site : null,
            'ma_quoc_gia' => $country !== '' ? $country : null,
        ];
        $replaced = [];
        foreach (['avatar' => ['url_anh_dai_dien', 'avatars', self::AVATAR_MAX], 'cover' => ['url_anh_bia', 'profile-covers', self::COVER_MAX]] as $input => [$column, $folder, $max]) {
            $upload = store_image_upload($_FILES[$input] ?? null, $folder, $max);
            if (isset($upload['error'])) {
                $this->fail(($input === 'avatar' ? t('settings.anh.dai.dien', [$upload['error']]) : t('settings.anh.bia', [$upload['error']])), '/settings#profile');
            }
            if ($upload) {
                $fields[$column] = $upload['path'];
                $replaced[] = $me[$column];
            } elseif (post_bool('remove_' . $input)) {
                $fields[$column] = null;
                $replaced[] = $me[$column];
            }
        }

        UserModel::update($uid, $fields);
        foreach ($replaced as $old) {
            delete_upload($old);
        }
        $_SESSION['user'] = UserModel::publicFields(UserModel::find($uid));
        audit('profile_update', 'nguoi_dung', $uid);
        flash('success',t('settings.da.luu.ho.so'));
        $this->redirect('/settings#profile');
    }

    public function password(array $params = []) {
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $current = (string)($_POST['current_password'] ?? '');
        $new = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        $hash = (string)Model::scalar('SELECT mat_khau_da_bam FROM nguoi_dung WHERE ma = :id', [':id' => $uid]);
        if (!password_verify($current, $hash)) {
            audit('password_change_fail', 'nguoi_dung', $uid);
            $this->fail(t('settings.password_wrong'), '/settings#password');
        }
        if (strlen($new) < 8 || strlen($new) > 72) {
            $this->fail(t('auth.mat.khau.moi.can.8.72.ky.tu'), '/settings#password');
        }
        if (!hash_equals($new, $confirm)) {
            $this->fail(t('auth.hai.lan.nhap.mat.khau.moi.khong.khop'), '/settings#password');
        }
        if (password_verify($new, $hash)) {
            $this->fail(t('settings.mat.khau.moi.phai.khac.mat.khau.hien.tai'), '/settings#password');
        }
        UserModel::update($uid, ['mat_khau_da_bam' => password_hash($new, PASSWORD_DEFAULT)]);
        session_regenerate_id(true);
        audit('password_change', 'nguoi_dung', $uid);
        NotificationModel::create($uid, 'account',t('settings.mat.khau.da.duoc.doi'),t('settings.neu.khong.phai.ban.doi.hay.lien.he.quan.tri.vien.ngay'), '/settings#password');
        flash('success',t('settings.password_updated'));
        $this->redirect('/settings#password');
    }

    public function privacy(array $params = []) {
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $visibility = ($_POST['pham_vi_hien_thi_ho_so'] ?? '') === 'private' ? 'private' : 'public';
        $lang = in_array($_POST['ngon_ngu_uu_tien'] ?? '', ['vi', 'en'], true) ? $_POST['ngon_ngu_uu_tien'] : 'vi';
        $currency = in_array($_POST['tien_te_uu_tien'] ?? '', ['USD', 'VND'], true) ? $_POST['tien_te_uu_tien'] : APP_CURRENCY;
        $theme = in_array($_POST['giao_dien'] ?? '', [THEME_DARK, THEME_LIGHT], true) ? $_POST['giao_dien'] : THEME_DEFAULT;
        Model::run('INSERT INTO cai_dat_nguoi_dung (nguoi_dung_id, pham_vi_hien_thi_ho_so, hien_thi_game_so_huu, cho_phep_nhan_tin, email_cap_nhat_san_pham, email_cap_nhat_cong_dong, ngon_ngu_uu_tien, tien_te_uu_tien, giao_dien)
            VALUES (:u, :v, :o, :m, :p, :c, :l, :cur, :g)
            ON DUPLICATE KEY UPDATE pham_vi_hien_thi_ho_so = VALUES(pham_vi_hien_thi_ho_so), hien_thi_game_so_huu = VALUES(hien_thi_game_so_huu),
                cho_phep_nhan_tin = VALUES(cho_phep_nhan_tin), email_cap_nhat_san_pham = VALUES(email_cap_nhat_san_pham),
                email_cap_nhat_cong_dong = VALUES(email_cap_nhat_cong_dong), ngon_ngu_uu_tien = VALUES(ngon_ngu_uu_tien), tien_te_uu_tien = VALUES(tien_te_uu_tien), giao_dien = VALUES(giao_dien)', [
            ':u' => $uid, ':v' => $visibility,
            ':o' => post_bool('hien_thi_game_so_huu') ? 1 : 0,
            ':m' => post_bool('cho_phep_nhan_tin') ? 1 : 0,
            ':p' => post_bool('email_cap_nhat_san_pham') ? 1 : 0,
            ':c' => post_bool('email_cap_nhat_cong_dong') ? 1 : 0,
            ':l' => $lang, ':cur' => $currency, ':g' => $theme,
        ]);
        theme_set($theme);
        // The language of the account is applied right away, so the page the
        // visitor lands on is already in the new language.
        Translator::set($lang);
        audit('settings_update', 'cai_dat_nguoi_dung', $uid);
        flash('success',t('settings.da.luu.quyen.rieng.tu.va.tuy.chon'));
        $this->redirect('/settings#privacy');
    }

    /** Self-service opt-in to publishing games. */
    public function developer(array $params = []) {
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        if (has_role('developer')) {
            $this->redirect('/dev');
        }
        if (!post_bool('agree')) {
            $this->fail(t('settings.hay.dong.y.voi.quy.dinh.danh.cho.nha.phat.trien'), '/settings#developer');
        }
        $role = RoleModel::findByCode('developer');
        if (!$role) {
            $this->fail(t('settings.he.thong.chua.co.vai.tro.nha.phat.trien'), '/settings#developer');
        }
        UserModel::addRole($uid, (int)$role['ma']);
        $_SESSION['user_roles'] = array_column(UserModel::getRoles($uid), 'ma_vai_tro');
        audit('developer_optin', 'nguoi_dung', $uid);
        flash('success',t('settings.ban.da.co.quyen.nha.phat.trien.hay.tao.tro.choi.dau.tien'));
        $this->redirect('/dev');
    }
}
