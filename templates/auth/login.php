<div class="auth">
    <form class="auth-card" method="post" action="<?= e(app_url('/auth/login')) ?>">
        <?= csrf_field() ?>
        <div>
            <h1><?= te('auth.login_button') ?></h1>
            <p class="muted lede"><?= te('auth.login.truy_cap_thu_vien_gio') ?></p>
        </div>
        <div class="field">
            <label for="login"><?= te('auth.login.email_hoac_ten_dang_nhap') ?></label>
            <input class="input" id="login" name="login" value="<?= e(old('login')) ?>" required maxlength="254" autocomplete="username" autofocus>
        </div>
        <div class="field">
            <label for="password"><?= te('auth.password') ?></label>
            <input class="input" id="password" type="password" name="password" required maxlength="72" autocomplete="current-password">
        </div>
        <button class="btn primary block" type="submit"><?= te('auth.login_button') ?></button>
        <p class="switch-line"><a href="<?= e(app_url('/auth/forgot-password')) ?>"><?= te('auth.forgot_password') ?></a></p>
        <p class="switch-line"><?= te('auth.no_account') ?> <a href="<?= e(app_url('/auth/register')) ?>"><?= te('auth.register_button') ?></a></p>
    </form>
</div>
