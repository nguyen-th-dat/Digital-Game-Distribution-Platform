<div class="auth">
    <form class="auth-card" method="post" action="<?= e(app_url('/auth/register')) ?>">
        <?= csrf_field() ?>
        <div>
            <h1><?= te('auth.register_button') ?></h1>
            <p class="muted lede"><?= te('auth.register.mot_tai_khoan_de_choi') ?></p>
        </div>
        <div class="field">
            <label for="display_name"><?= te('settings.display_name') ?></label>
            <input class="input" id="display_name" name="display_name" value="<?= e(old('display_name')) ?>" required maxlength="100" autocomplete="nickname">
        </div>
        <div class="field">
            <label for="username"><?= te('auth.username') ?></label>
            <input class="input" id="username" name="username" value="<?= e(old('username')) ?>" required minlength="3" maxlength="30" pattern="[A-Za-z0-9_]{3,30}" autocomplete="username">
            <span class="hint"><?= te('auth.register.3_30_ky_tu_chu') ?></span>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input class="input" id="email" type="email" name="email" value="<?= e(old('email')) ?>" required maxlength="254" autocomplete="email">
        </div>
        <div class="field">
            <label for="password"><?= te('auth.password') ?></label>
            <input class="input" id="password" type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password">
            <span class="hint"><?= te('auth.register.it_nhat_8_ky_tu') ?></span>
        </div>
        <div class="field">
            <label for="confirm_password"><?= te('auth.register.nhap_lai_mat_khau') ?></label>
            <input class="input" id="confirm_password" type="password" name="confirm_password" required minlength="8" maxlength="72" autocomplete="new-password">
        </div>
        <button class="btn primary block" type="submit"><?= te('auth.register_button') ?></button>
        <p class="switch-line"><?= te('auth.have_account') ?> <a href="<?= e(app_url('/auth/login')) ?>"><?= te('auth.login_button') ?></a></p>
    </form>
</div>
