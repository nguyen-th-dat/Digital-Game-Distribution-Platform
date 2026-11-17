<div class="auth">
    <div class="auth-card">
        <div>
            <h1><?= te('auth.forgot-password-account.kiem_tra_tai_khoan') ?></h1>
            <p class="muted lede"><?= te('auth.forgot-password-account.day_la_tai_khoan_dang') ?> <strong><?= e($email) ?></strong><?= te('auth.forgot-password-account.xac_nhan_dung_tai_khoan') ?></p>
        </div>
        <div class="account-preview">
            <img class="avatar lg" src="<?= e(avatar_url($user)) ?>" alt="">
            <div class="grow">
                <strong class="account-name">@<?= e($user['ten_dang_nhap']) ?></strong>
                <span class="muted"><?= e($user['ten_hien_thi']) ?></span>
                <span class="muted"><?= e($user['email']) ?></span>
            </div>
        </div>
        <form method="post" action="<?= e(app_url('/auth/forgot-password/send')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="email" value="<?= e($email) ?>">
            <button class="btn primary block" type="submit"><?= te('auth.forgot-password-account.tiep_tuc_quen_mat_khau') ?></button>
        </form>
        <p class="switch-line"><a href="<?= e(app_url('/auth/forgot-password')) ?>"><?= te('auth.forgot-password-account.khong_phai_tai_khoan_cua') ?></a></p>
    </div>
</div>
