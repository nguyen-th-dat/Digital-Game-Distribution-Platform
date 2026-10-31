<?php
// Layout template for itch.io clone
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? APP_NAME, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        :root {
            color-scheme: light;
            --ink: #211b36;
            --muted: #716b82;
            --paper: #f3f0e9;
            --line: #e1dce8;
            --purple: #7047eb;
            --purple-dark: #5231c7;
            --coral: #ff715b;
            --mint: #b7f2d5;
            --sun: #ffe28a;
            --shadow: 0 18px 46px rgba(43, 31, 77, .11);
            --shadow-soft: 0 8px 26px rgba(43, 31, 77, .055);
            --radius: 20px;
            --surface: #f6f3ed;
            --surface-raised: #f9f7f2;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            color: var(--ink);
            font: 16px/1.65 "Segoe UI", Inter, system-ui, -apple-system, BlinkMacSystemFont, Arial, sans-serif;
            background:
                radial-gradient(ellipse at 4% 1%, rgba(228, 207, 159, .2), transparent 30rem),
                radial-gradient(ellipse at 96% 8%, rgba(168, 205, 190, .18), transparent 32rem),
                linear-gradient(180deg, #eeebf1 0%, #eae7ee 55%, #efedea 100%);
        }
        a { color: var(--purple-dark); text-decoration: none; transition: color .18s ease, background .18s ease, border-color .18s ease; }
        a:hover { color: var(--coral); }
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 3px solid rgba(112, 71, 235, .28);
            outline-offset: 2px;
        }
        .container { width: min(1200px, 100% - 48px); margin: 0 auto; }
        .site-header {
            position: sticky; z-index: 20; top: 0; padding: 12px 0; margin-bottom: 34px;
            border-bottom: 1px solid rgba(112, 71, 235, .1);
            background: rgba(239, 236, 243, .92); backdrop-filter: blur(18px);
        }
        .header-inner { display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .site-header h1 { margin: 0; font-size: 18px; line-height: 1.2; letter-spacing: -.04em; }
        .brand { display: inline-flex; align-items: center; gap: 11px; color: var(--ink); }
        .brand:hover { color: var(--ink); }
        .brand-mark {
            display: grid; width: 42px; height: 42px; place-items: center; border-radius: 15px;
            color: #fff; background: linear-gradient(135deg, var(--purple), #a36cff);
            box-shadow: 0 7px 16px rgba(112, 71, 235, .24); font-size: 20px;
        }
        nav { display: flex; align-items: center; justify-content: flex-end; gap: 5px; flex-wrap: wrap; }
        nav form { margin: 0; }
        nav a, .nav-logout {
            display: inline-flex; align-items: center; min-height: 42px; padding: 8px 13px;
            border: 0; border-radius: 13px; color: #56536b; background: transparent;
            font: inherit; font-size: 14px; font-weight: 650; cursor: pointer; transition: color .18s ease, background .18s ease;
        }
        nav a:hover, .nav-logout:hover { color: var(--purple-dark); background: #f0ebff; text-decoration: none; }
        nav .nav-cta { color: #fff; background: linear-gradient(135deg, var(--purple), #875df2); box-shadow: 0 5px 13px rgba(112, 71, 235, .18); }
        nav .nav-cta:hover { color: #fff; background: var(--purple-dark); }
        nav .nav-profile { width: 44px; height: 44px; min-height: 44px; padding: 2px; border-radius: 50%; }
        nav .nav-profile:hover { background: #f0ebff; }
        .nav-profile img { display: block; width: 40px; height: 40px; border: 2px solid #fff; border-radius: 50%; object-fit: cover; box-shadow: 0 2px 8px rgba(48, 34, 86, .18); }
        .notification-badge { display: inline-block; min-width: 18px; padding: 1px 5px; border-radius: 999px; color: #fff; background: #7047eb; font-size: 11px; line-height: 1.5; text-align: center; vertical-align: 1px; }
        main { min-height: 65vh; animation: page-enter .35s ease both; }
        main > section { margin-bottom: 30px; }
        footer { margin-top: 76px; padding: 26px 0 38px; border-top: 1px solid var(--line); color: var(--muted); font-size: 14px; }
        footer p { margin: 0; }
        .button {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            min-height: 46px; padding: 10px 19px; border: 0; border-radius: 14px;
            color: #fff; background: linear-gradient(135deg, var(--purple), #875df2); box-shadow: 0 8px 17px rgba(112, 71, 235, .19);
            font: inherit; font-weight: 700; cursor: pointer; transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
        }
        .button:hover { color: #fff; background: linear-gradient(135deg, var(--purple-dark), #7047eb); box-shadow: 0 11px 22px rgba(82, 49, 199, .22); transform: translateY(-2px); text-decoration: none; }
        .card {
            padding: 22px; border: 1px solid rgba(91, 73, 139, .1); border-radius: var(--radius);
            background: rgba(246, 243, 237, .94); box-shadow: var(--shadow-soft);
        }
        a.card { color: inherit; }
        a.card:hover { border-color: rgba(112, 71, 235, .28); box-shadow: var(--shadow); text-decoration: none; transform: translateY(-3px); }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(235px, 1fr)); gap: 20px; }
        h1, h2, h3, h4 { color: var(--ink); line-height: 1.24; letter-spacing: -.03em; }
        h2 { margin-top: 0; font-size: clamp(26px, 3vw, 36px); }
        h3 { font-size: clamp(19px, 2vw, 23px); }
        p { color: #57556a; }
        input, textarea, select {
            max-width: 100%; padding: 12px 14px; border: 1px solid #ded9e8;
            border-radius: 13px; color: var(--ink); background: var(--surface-raised); font: inherit; transition: border-color .18s ease, box-shadow .18s ease;
        }
        input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]) { min-height: 44px; }
        input::placeholder { color: #a09cad; }
        input:hover, textarea:hover, select:hover { border-color: #bdb1df; }
        input:focus, textarea:focus, select:focus { border-color: var(--purple); box-shadow: 0 0 0 4px rgba(112, 71, 235, .09); outline: 0; }
        textarea { resize: vertical; }
        label { display: block; margin: 14px 0 6px; color: #454258; font-size: 14px; font-weight: 650; }
        .notice { padding: 14px 17px; margin: 12px 0 20px; border: 1px solid #f1d88a; border-radius: 15px; background: #fff7da; color: #735914; box-shadow: 0 5px 16px rgba(115, 89, 20, .06); }
        .notice-success { border-color: #b7e7ce; background: #edfbf3; color: #236b45; }
        .notice-error { border-color: #f1c4c0; background: #fff1ef; color: #9c3d35; }
        img { max-width: 100%; }
        figure { margin: 18px 0; }
        figure img { border-radius: 16px; }
        article.card + article.card { margin-top: 14px; }
        dl { line-height: 1.55; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid var(--line); text-align: left; }
        th { color: var(--muted); font-size: 13px; letter-spacing: .04em; text-transform: uppercase; }
        ::selection { color: #fff; background: #7651df; }
        @keyframes page-enter { from { opacity: .7; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 760px) {
            .container { width: min(100% - 30px, 640px); }
            .site-header { position: relative; margin-bottom: 24px; }
            .header-inner { align-items: flex-start; flex-direction: column; gap: 12px; }
            nav { justify-content: flex-start; gap: 4px; }
            nav a, .nav-logout { min-height: 40px; padding: 7px 10px; }
            nav .nav-profile { margin-left: 2px; }
            .card { padding: 18px; border-radius: 17px; }
            .grid { grid-template-columns: repeat(auto-fill, minmax(min(100%, 220px), 1fr)); gap: 14px; }
            footer { margin-top: 52px; }
        }
        @media (max-width: 420px) {
            .container { width: calc(100% - 24px); }
            nav { width: 100%; }
            nav form { display: contents; }
            nav a, .nav-logout { padding-right: 8px; padding-left: 8px; font-size: 13px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="site-header">
            <div class="header-inner">
            <h1><a class="brand" href="<?php echo app_url('/games'); ?>"><span class="brand-mark" aria-hidden="true">✦</span><span><?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?></span></a></h1>
            <nav>
                <a href="<?php echo app_url('/games'); ?>">Trò chơi</a>
                <a href="<?php echo app_url('/forums'); ?>">Diễn đàn</a>
                <a href="<?php echo app_url('/jams'); ?>">Cuộc thi</a>
                <?php if (!empty($_SESSION['user'])): ?>
                    <a href="<?php echo app_url('/account'); ?>">Tài khoản</a>
                    <?php $navUnreadCount = NotificationModel::unreadCount((int)$_SESSION['user']['ma']); ?>
                    <a href="<?php echo app_url('/notifications'); ?>">Thông báo<?php if ($navUnreadCount > 0): ?> <span class="notification-badge"><?php echo $navUnreadCount > 99 ? '99+' : $navUnreadCount; ?></span><?php endif; ?></a>
                    <a href="<?php echo app_url('/cart'); ?>">Giỏ hàng</a>
                    <?php if (in_array('admin', $_SESSION['user_roles'] ?? [], true)): ?>
                        <a href="<?php echo app_url('/admin'); ?>">Quản trị</a>
                    <?php endif; ?>
                    <form method="post" action="<?php echo app_url('/auth/logout'); ?>">
                        <button class="nav-logout" type="submit">Đăng xuất</button>
                    </form>
                    <?php
                    $navUser = $_SESSION['user'];
                    $avatarUrl = !empty($navUser['url_anh_dai_dien'])
                        ? $navUser['url_anh_dai_dien']
                        : app_url('/avatars/' . (int)$navUser['ma']);
                    ?>
                    <a class="nav-profile" href="<?php echo app_url('/account/profile'); ?>" aria-label="Xem hồ sơ của <?php echo htmlspecialchars($navUser['ten_hien_thi'] ?? 'tài khoản', ENT_QUOTES, 'UTF-8'); ?>" title="Hồ sơ cá nhân">
                        <img src="<?php echo htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="">
                    </a>
                <?php else: ?>
                    <a href="<?php echo app_url('/auth/login'); ?>">Đăng nhập</a>
                    <a class="nav-cta" href="<?php echo app_url('/auth/register'); ?>">Đăng ký</a>
                <?php endif; ?>
            </nav>
            </div>
        </header>
        <main>
            <?php foreach (['error', 'success'] as $flash): ?>
                <?php if (!empty($_SESSION[$flash])): ?>
                    <div class="notice notice-<?php echo $flash === 'success' ? 'success' : 'error'; ?>"><?php echo htmlspecialchars((string)$_SESSION[$flash], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php unset($_SESSION[$flash]); ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php echo $content; ?>
        </main>
        <footer>
            <p>Nen Tang Game Doc Lap Vi - Mô phỏng itch.io</p>
        </footer>
    </div>
</body>
</html>
