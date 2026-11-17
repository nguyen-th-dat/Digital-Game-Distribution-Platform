<?php
/** Header and section tabs of a developer project. @var array $game @var string $tab */
$gid = (int)$game['ma'];
$base = '/dev/games/' . $gid;
$sections = [
    'details' => ['', t('dev.game-tabs.chi.tiet')],
    'media' => ['/media', t('dev.game-tabs.anh.video')],
    'downloads' => ['/downloads', t('dev.game-tabs.lien.ket.tai.ve')],
    'authors' => ['/authors', t('dev.game-tabs.tac.gia')],
    'releases' => ['/releases', t('dev.game-tabs.phien.ban')],
    'devlogs' => ['/devlogs', t('dev.game-tabs.nhat.ky')],
    'page' => ['/page', t('dev.game-tabs.giao.dien.trang')],
    'keys' => ['/keys', t('dev.game-tabs.ma.truy.cap')],
    'stats' => ['/stats', t('dev.game-tabs.thong.ke')],
];
$isPublic = in_array($game['trang_thai'], ['published', 'unlisted'], true);
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url('/dev/games')) ?>"><?= te('dev.games') ?></a><span><?= e($sections[$tab][1] ?? '') ?></span></nav>
<div class="title-row">
    <div>
        <h1><?= e($game['tieu_de']) ?></h1>
        <p class="sub"><?= status_pill('game_status', $game['trang_thai']) ?> <?= e(label('project_type', $game['loai_du_an'])) ?> · <?= e(label('pricing', $game['mo_hinh_gia'])) ?><?= (float)$game['gia_toi_thieu'] > 0 ? ' ' . e(money($game['gia_toi_thieu'])) : '' ?></p>
    </div>
    <?php if ($isPublic): ?>
        <a class="btn" href="<?= e(game_url($game)) ?>"><?= te('admin.jam-form.xem_trang_cong_khai') ?></a>
    <?php endif; ?>
</div>
<nav class="tabs" aria-label="<?= te('partials.dev-game-tabs.quan_ly_du_an') ?>">
    <?php foreach ($sections as $key => [$path, $text]): ?>
        <a href="<?= e(app_url($base . $path)) ?>"<?= $tab === $key ? ' aria-selected="true"' : '' ?>><?= e($text) ?></a>
    <?php endforeach; ?>
</nav>
