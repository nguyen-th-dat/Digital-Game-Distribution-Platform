<div class="auth">
    <form class="auth-card" method="post" action="<?= e(app_url('/auth/forgot-password/check')) ?>">
        <?= csrf_field() ?>
        <div>
            <h1><?= te('auth.forgot_title') ?></h1>
            <p class="muted lede"><?= te('auth.forgot-password.nhap_email_cua_ban_de') ?></p>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input class="input" id="email" type="email" name="email" value="<?= e(old('email')) ?>" required maxlength="254" autocomplete="email" autofocus>
        </div>
        <button class="btn primary block" type="submit"><?= te('auth.forgot-password.kiem_tra') ?></button>
        <p class="switch-line"><?= te('auth.forgot-password.da_nho_mat_khau') ?> <a href="<?= e(app_url('/auth/login')) ?>"><?= te('auth.login_button') ?></a></p>
    </form>
</div>
