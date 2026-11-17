<?php
$user = current_user();
$roles = user_roles();
$isDev = in_array('developer', $roles, true);
$isAdmin = in_array('admin', $roles, true);
$isMod = $isAdmin || in_array('moderator', $roles, true);
$counts = shell_counts();
$flashes = take_flashes();
$pageTitle = isset($pageTitle) && $pageTitle !== '' ? $pageTitle . ' · ' . APP_NAME : APP_NAME;
$searchValue = current_path() === '/games' ? (string)($_GET['q'] ?? '') : '';
$theme = theme_preference();
$themeNext = theme_toggle_target($theme);
$locale = current_locale();
?>
<!doctype html>
<html lang="<?= e(locale_html_lang()) ?>" data-theme="<?= e($theme) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? t('app.tagline')) ?>">
    <meta name="theme-color" content="<?= $theme === THEME_LIGHT ? '#f4f4f5' : '#0d0d0f' ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="<?= e(app_url('/public/assets/app.css')) ?>?v=<?= (int)@filemtime(__DIR__ . '/../public/assets/app.css') ?>">
</head>
<body>
<a class="sr-only" href="#main"><?= te('nav.skip_to_content') ?></a>
<div class="app">
    <aside class="sidebar" id="sidebar" aria-label="<?= te('nav.main') ?>">
        <div class="brand">
            <span class="brand-mark" aria-hidden="true">K</span>
            <a href="<?= e(app_url('/')) ?>"><?= e(APP_NAME) ?></a>
            <span class="spacer"></span>
            <button type="button" class="menu-toggle close" data-menu-close aria-label="<?= te('nav.close_menu') ?>"><?= te('app.text') ?></button>
        </div>
        <nav class="nav">
            <div class="group-label"><?= te('nav.store') ?></div>
            <a class="nav-item" href="<?= e(app_url('/')) ?>"<?= nav_current('/') ?>><?= te('nav.home') ?></a>
            <a class="nav-item" href="<?= e(app_url('/games')) ?>"<?= nav_current('/games', true) ?>><?= te('nav.explore') ?></a>
            <a class="nav-item" href="<?= e(app_url('/games?sale=1')) ?>"><?= te('nav.on_sale') ?></a>
            <a class="nav-item" href="<?= e(app_url('/bundles')) ?>"<?= nav_current('/bundles', true) ?>><?= te('nav.bundles') ?></a>

            <div class="group-label"><?= te('nav.community') ?></div>
            <a class="nav-item" href="<?= e(app_url('/forums')) ?>"<?= nav_current('/forums', true) ?>><?= te('nav.forums') ?></a>
            <a class="nav-item" href="<?= e(app_url('/jams')) ?>"<?= nav_current('/jams', true) ?>><?= te('nav.jams') ?></a>
            <a class="nav-item" href="<?= e(app_url('/collections')) ?>"<?= nav_current('/collections', true) ?>><?= te('nav.collections') ?></a>

            <?php if ($user): ?>
                <div class="group-label"><?= te('nav.mine') ?></div>
                <a class="nav-item" href="<?= e(app_url('/library')) ?>"<?= nav_current('/library') ?>><?= te('nav.library') ?></a>
                <a class="nav-item" href="<?= e(app_url('/wishlist')) ?>"<?= nav_current('/wishlist') ?>><?= te('nav.wishlist') ?></a>
                <a class="nav-item" href="<?= e(app_url('/orders')) ?>"<?= nav_current('/orders', true) ?>><?= te('nav.orders') ?></a>
                <a class="nav-item" href="<?= e(app_url('/notifications')) ?>"<?= nav_current('/notifications') ?>><?= te('nav.notifications') ?><?php if ($counts['unread']): ?><span class="count"><?= fmt_int($counts['unread']) ?></span><?php endif; ?></a>
                <a class="nav-item" href="<?= e(app_url('/settings')) ?>"<?= nav_current('/settings') ?>><?= te('nav.settings') ?></a>
            <?php endif; ?>

            <?php if ($isDev): ?>
                <div class="group-label"><?= te('nav.developer') ?></div>
                <a class="nav-item" href="<?= e(app_url('/dev')) ?>"<?= nav_current('/dev') ?>><?= te('nav.overview') ?></a>
                <a class="nav-item" href="<?= e(app_url('/dev/games')) ?>"<?= nav_current('/dev/games', true) ?>><?= te('nav.projects') ?></a>
                <a class="nav-item" href="<?= e(app_url('/dev/sales')) ?>"<?= nav_current('/dev/sales') ?>><?= te('nav.revenue') ?></a>
                <a class="nav-item" href="<?= e(app_url('/dev/payouts')) ?>"<?= nav_current('/dev/payouts') ?>><?= te('nav.payouts') ?></a>
                <a class="nav-item" href="<?= e(app_url('/dev/coupons')) ?>"<?= nav_current('/dev/coupons') ?>><?= te('nav.coupons') ?></a>
                <a class="nav-item" href="<?= e(app_url('/dev/bundles')) ?>"<?= nav_current('/dev/bundles', true) ?>><?= te('nav.my_bundles') ?></a>
            <?php endif; ?>

            <?php if ($isMod): ?>
                <div class="group-label"><?= te('nav.admin') ?></div>
                <a class="nav-item" href="<?= e(app_url('/admin')) ?>"<?= nav_current('/admin') ?>><?= te('nav.overview') ?></a>
                <a class="nav-item" href="<?= e(app_url('/admin/reports')) ?>"<?= nav_current('/admin/reports', true) ?>><?= te('nav.reports') ?></a>
                <a class="nav-item" href="<?= e(app_url('/admin/games')) ?>"<?= nav_current('/admin/games') ?>><?= te('nav.games') ?></a>
                <a class="nav-item" href="<?= e(app_url('/admin/comments')) ?>"<?= nav_current('/admin/comments') ?>><?= te('nav.content') ?></a>
                <?php if ($isAdmin): ?>
                    <a class="nav-item" href="<?= e(app_url('/admin/users')) ?>"<?= nav_current('/admin/users', true) ?>><?= te('nav.users') ?></a>
                    <a class="nav-item" href="<?= e(app_url('/admin/orders')) ?>"<?= nav_current('/admin/orders') ?>><?= te('nav.orders') ?></a>
                    <a class="nav-item" href="<?= e(app_url('/admin/refunds')) ?>"<?= nav_current('/admin/refunds') ?>><?= te('nav.refunds') ?></a>
                    <a class="nav-item" href="<?= e(app_url('/admin/payouts')) ?>"<?= nav_current('/admin/payouts') ?>><?= te('nav.payouts') ?></a>
                    <a class="nav-item" href="<?= e(app_url('/admin/campaigns')) ?>"<?= nav_current('/admin/campaigns', true) ?>><?= te('nav.campaigns') ?></a>
                    <a class="nav-item" href="<?= e(app_url('/admin/coupons')) ?>"<?= nav_current('/admin/coupons') ?>><?= te('nav.coupons') ?></a>
                    <a class="nav-item" href="<?= e(app_url('/admin/bundles')) ?>"<?= nav_current('/admin/bundles') ?>><?= te('nav.bundles') ?></a>
                    <a class="nav-item" href="<?= e(app_url('/admin/jams')) ?>"<?= nav_current('/admin/jams') ?>><?= te('nav.jams') ?></a>
                    <a class="nav-item" href="<?= e(app_url('/admin/taxonomy')) ?>"<?= nav_current('/admin/taxonomy') ?>><?= te('nav.taxonomy') ?></a>
                    <a class="nav-item" href="<?= e(app_url('/admin/audit')) ?>"<?= nav_current('/admin/audit') ?>><?= te('nav.audit') ?></a>
                <?php endif; ?>
                <a class="nav-item" href="<?= e(app_url('/admin/moderation')) ?>"<?= nav_current('/admin/moderation') ?>><?= te('nav.moderation_log') ?></a>
            <?php endif; ?>
        </nav>
        <div class="bottom"><?= te('app.sandbox_note') ?></div>
    </aside>

    <div class="main">
        <header class="topbar">
            <button type="button" class="menu-toggle" data-menu-open aria-controls="sidebar" aria-expanded="false" aria-label="<?= te('nav.open_menu') ?>">
                <svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true"><path d="M3 6h14M3 10h14M3 14h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>
            <form class="search" action="<?= e(app_url('/games')) ?>" method="get" role="search">
                <label class="sr-only" for="top-search"><?= te('nav.search') ?></label>
                <input class="input" id="top-search" type="search" name="q" value="<?= e($searchValue) ?>" placeholder="<?= te('nav.search_placeholder') ?>" maxlength="100">
            </form>
            <span class="spacer"></span>
            <nav class="topbar-links" aria-label="<?= te('nav.account') ?>">
                <form class="theme-toggle-form" method="post" action="<?= e(app_url('/theme/toggle')) ?>">
                    <?= csrf_field() ?>
                    <?= return_field() ?>
                    <input type="hidden" name="theme" value="<?= e($themeNext) ?>">
                    <button type="submit" class="theme-toggle" data-theme-toggle aria-label="<?= $themeNext === THEME_LIGHT ? te('nav.theme_to_light') : te('nav.theme_to_dark') ?>" title="<?= $themeNext === THEME_LIGHT ? te('settings.theme_light') : te('settings.theme_dark') ?>">
                        <svg class="icon-sun" width="18" height="18" viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="4" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M10 1.5v2M10 16.5v2M18.5 10h-2M3.5 10h-2M16 4l-1.4 1.4M5.4 14.6 4 16M16 16l-1.4-1.4M5.4 5.4 4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        <svg class="icon-moon" width="18" height="18" viewBox="0 0 20 20" aria-hidden="true"><path d="M16.5 12.4A7 7 0 0 1 7.6 3.5a7 7 0 1 0 8.9 8.9z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    </button>
                </form>
                <details class="locale-switcher">
                    <summary aria-label="<?= te('nav.switch_language') ?>" title="<?= te('nav.switch_language') ?>">
                        <svg width="18" height="18" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 2.5a7.5 7.5 0 1 0 0 15 7.5 7.5 0 0 0 0-15zM2.5 10h15M10 2.5c2 2.4 2 12.6 0 15M10 2.5c-2 2.4-2 12.6 0 15" fill="none" stroke="currentColor" stroke-width="1.4"/></svg>
                        <span class="locale-code"><?= e(strtoupper($locale)) ?></span>
                    </summary>
                    <div class="menu">
                        <?php foreach (Translator::available() as $code => $name): ?>
                            <form method="post" action="<?= e(app_url('/locale/switch')) ?>">
                                <?= csrf_field() ?>
                                <?= return_field() ?>
                                <input type="hidden" name="locale" value="<?= e($code) ?>">
                                <button type="submit"<?= $code === $locale ? ' aria-current="true" class="is-active"' : '' ?>><?= e($name) ?></button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </details>
                <?php if ($user): ?>
                    <a class="top-link hide-mobile" href="<?= e(app_url('/library')) ?>"<?= nav_current('/library') ?>><?= te('nav.library') ?></a>
                    <a class="top-link" href="<?= e(app_url('/cart')) ?>"<?= nav_current('/cart') ?>><?= te('nav.cart') ?><span class="badge"><?= fmt_int($counts['cart']) ?></span></a>
                    <details class="account">
                        <summary aria-label="<?= te('nav.account') ?>">
                            <img class="avatar" src="<?= e(avatar_url($user)) ?>" alt="">
                            <span class="name"><?= e($user['ten_hien_thi']) ?></span>
                        </summary>
                        <div class="menu">
                            <div class="who"><?= e($user['ten_hien_thi']) ?><span>@<?= e($user['ten_dang_nhap']) ?></span></div>
                            <a href="<?= e(profile_url($user)) ?>"><?= te('nav.public_profile') ?></a>
                            <a href="<?= e(app_url('/notifications')) ?>"><?= te('nav.notifications') ?><?= $counts['unread'] ? ' (' . fmt_int($counts['unread']) . ')' : '' ?></a>
                            <a href="<?= e(app_url('/collections')) ?>"><?= te('nav.collections') ?></a>
                            <a href="<?= e(app_url('/settings')) ?>"><?= te('nav.settings') ?></a>
                            <?php if ($isDev): ?><a href="<?= e(app_url('/dev')) ?>"><?= te('nav.dev_board') ?></a><?php endif; ?>
                            <?php if ($isMod): ?><a href="<?= e(app_url('/admin')) ?>"><?= te('nav.admin_panel') ?></a><?php endif; ?>
                            <form method="post" action="<?= e(app_url('/auth/logout')) ?>">
                                <?= csrf_field() ?>
                                <button type="submit"><?= te('nav.logout') ?></button>
                            </form>
                        </div>
                    </details>
                <?php else: ?>
                    <a class="top-link" href="<?= e(app_url('/auth/login')) ?>"<?= nav_current('/auth/login') ?>><?= te('nav.login') ?></a>
                    <a class="btn primary" href="<?= e(app_url('/auth/register')) ?>"><?= te('nav.register') ?></a>
                <?php endif; ?>
            </nav>
        </header>

        <main class="canvas" id="main">
            <?php if ($flashes): ?>
                <div class="flashes" role="status">
                    <?php foreach ($flashes as $flash): ?>
                        <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?= $content ?>
        </main>

        <footer class="site-foot">
            <span><?= e(APP_FULL_NAME) ?></span>
            <span><?= te('common.photo_source_note') ?></span>
            <span><?= te('app.currency_note') ?></span>
        </footer>
    </div>
</div>
<script src="<?= e(app_url('/public/assets/app.js')) ?>?v=<?= (int)@filemtime(__DIR__ . '/../public/assets/app.js') ?>" defer></script>
</body>
</html>
