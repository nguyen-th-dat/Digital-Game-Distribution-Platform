<div class="auth">
    <div class="auth-card">
        <span class="faint"><?= te('errors.404.loi', [ (int)($status ?? 404) ]) ?></span>
        <h1><?= e($pageTitle ?? te('errors.403.da_co_loi')) ?></h1>
        <p class="muted"><?= e($message) ?></p>
        <div class="form-actions">
            <a class="btn primary" href="<?= e(app_url('/')) ?>"><?= te('error.back_home') ?></a>
            <a class="btn" href="<?= e(app_url('/games')) ?>"><?= te('bundle.index.kham_pha_tro_choi') ?></a>
        </div>
    </div>
</div>
