<?php
$escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$avatarUrl = !empty($author['url_anh_dai_dien'])
    ? $author['url_anh_dai_dien']
    : app_url('/avatars/' . (int)$author['ma']);
?>
<section class="player-profile">
    <p><a href="<?php echo app_url('/games'); ?>">← Trò chơi</a></p>
    <header class="player-profile-header">
        <img class="player-profile-avatar" src="<?php echo $escape($avatarUrl); ?>" alt="Ảnh đại diện của <?php echo $escape($author['ten_hien_thi']); ?>">
        <div>
            <h2><?php echo $escape($author['ten_hien_thi']); ?></h2>
            <?php if (!empty($author['gioi_thieu'])): ?><p><?php echo nl2br($escape($author['gioi_thieu'])); ?></p><?php endif; ?>
            <p class="player-joined">Tham gia ngày <?php echo $escape(date('d/m/Y', strtotime($author['ngay_tao']))); ?></p>
            <?php if (!empty($currentUser) && (int)$currentUser['ma'] !== (int)$author['ma']): ?>
                <form method="post" action="<?php echo app_url('/author/' . (int)$author['ma'] . '/follow'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $escape($csrfToken); ?>">
                    <button class="button" type="submit"><?php echo $isFollowing ? 'Bỏ theo dõi' : 'Theo dõi người chơi'; ?></button>
                </form>
            <?php elseif (empty($currentUser)): ?>
                <p><a href="<?php echo app_url('/auth/login'); ?>">Đăng nhập</a> để theo dõi người chơi và nhận thông báo.</p>
            <?php endif; ?>
        </div>
    </header>
    <h3>Trò chơi đã đăng</h3>
    <?php if (!$games): ?><p>Chưa có trò chơi được công bố.</p><?php else: ?>
        <ul><?php foreach ($games as $game): ?><li><a href="<?php echo app_url('/games/' . rawurlencode($game['duong_dan_rut_gon'])); ?>"><?php echo $escape($game['tieu_de']); ?></a></li><?php endforeach; ?></ul>
    <?php endif; ?>
</section>
<style>
    .player-profile { max-width: 900px; margin: auto; }
    .player-profile-header { display: flex; align-items: center; gap: 20px; padding: 22px; border: 1px solid var(--line); border-radius: 20px; background: rgba(255, 255, 255, .88); }
    .player-profile-avatar { width: 96px; height: 96px; flex: 0 0 96px; border-radius: 50%; object-fit: cover; }
    .player-profile-header h2, .player-profile-header p { margin: 0; }
    .player-profile-header h2 { margin-bottom: 8px; }
    .player-profile-header .player-joined { margin-top: 10px; color: var(--muted); font-size: 14px; }
    @media (max-width: 520px) { .player-profile-header { align-items: flex-start; flex-direction: column; } }
</style>
