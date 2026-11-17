<div class="auth">
    <div class="auth-card">
        <form method="post" action="<?= e(app_url('/auth/reset-password')) ?>">
            <?= csrf_field() ?>
            <div>
                <h1><?= te('auth.reset_title') ?></h1>
                <p class="muted lede"><?= te('auth.reset-password.nhap_ma_xac_nhan_da') ?></p>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input class="input" id="email" type="email" name="email" value="<?= e(old('email') ?: $email) ?>" required maxlength="254" autocomplete="email"<?= $email !== '' ? ' readonly' : '' ?>>
            </div>
            <div class="field">
                <label for="code"><?= te('auth.reset-password.ma_xac_nhan') ?></label>
                <input class="input" id="code" name="code" value="<?= e(old('code')) ?>" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" autofocus>
                <span class="hint"><?= te('auth.reset-password.ma_gom_6_chu_so') ?></span>
            </div>
            <div class="field">
                <label for="password"><?= te('auth.new_password') ?></label>
                <input class="input" id="password" type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password">
                <span class="hint"><?= te('auth.register.it_nhat_8_ky_tu') ?></span>
            </div>
            <div class="field">
                <label for="confirm_password"><?= te('auth.reset-password.nhap_lai_mat_khau_moi') ?></label>
                <input class="input" id="confirm_password" type="password" name="confirm_password" required minlength="8" maxlength="72" autocomplete="new-password">
            </div>
            <button class="btn primary block" type="submit"><?= te('auth.reset_title') ?></button>
        </form>
        <form class="resend-form" method="post" action="<?= e(app_url('/auth/forgot-password/send')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="email" value="<?= e($email) ?>">
            <span class="muted"><?= te('auth.reset-password.chua_nhan_duoc_ma') ?></span>
            <button class="link-button" type="submit" data-resend<?= $resendWait > 0 ? ' data-wait="' . $resendWait . '" disabled' : '' ?>><?= $resendWait > 0 ? te('auth.reset-password.vui_long_doi') . ' ' . $resendWait . ' ' . te('auth.reset-password.giay') : te('auth.reset-password.gui_lai_ma') ?></button>
        </form>
    </div>
</div>
