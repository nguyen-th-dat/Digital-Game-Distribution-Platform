<?php
/**
 * Download page of a game.
 * @var array $game @var bool $owned @var array $links @var array $releases
 */
$slug = $game['duong_dan_rut_gon'];
$base = '/games/' . rawurlencode($slug);
$playUrl = game_play_url($game);
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>">
    <a href="<?= e(app_url('/')) ?>"><?= te('home.title') ?></a>
    <a href="<?= e(app_url('/games')) ?>"><?= te('game.explore') ?></a>
    <span><?= e($game['tieu_de']) ?></span>
</nav>

<div class="title-row">
    <div>
        <h1>Tải <?= e($game['tieu_de']) ?></h1>
        <p class="sub"><?= te('game.downloads.chon_mot_lien_ket_de') ?></p>
    </div>
    <div class="cluster">
        <?php if ($owned && $playUrl): ?>
            <a class="btn primary" href="<?= e($playUrl) ?>" target="_blank" rel="noopener noreferrer"><?= te('game.downloads.choi_tren_trinh_duyet') ?></a>
        <?php endif; ?>
        <a class="btn" href="<?= e(app_url($base)) ?>"><?= te('devlog.index.ve_trang_tro_choi') ?></a>
    </div>
</div>

<?php if (!$owned): ?>
    <section class="panel">
        <div class="empty">
            <p><?= te('game.downloads.ban_can_so_huu_tro') ?></p>
            <?php if (!current_user()): ?>
                <a class="btn primary" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($base . '/downloads'))) ?>"><?= te('auth.login_button') ?></a>
            <?php else: ?>
                <a class="btn primary" href="<?= e(app_url($base)) ?>"><?= te('game.downloads.mua_hoac_nhan_tro_choi') ?></a>
            <?php endif; ?>
        </div>
    </section>
<?php elseif (!$links): ?>
    <section class="panel">
        <div class="empty">
            <?php if ($playUrl): ?>
                <p><?= te('game.downloads.tro_choi_nay_chay_ngay') ?></p>
                <a class="btn primary" href="<?= e($playUrl) ?>" target="_blank" rel="noopener noreferrer"><?= te('game.downloads.choi_tren_trinh_duyet') ?></a>
            <?php else: ?>
                <p><?= te('game.downloads.nha_phat_trien_chua_dang') ?></p>
                <a class="btn" href="<?= e(app_url($base)) ?>"><?= te('devlog.index.ve_trang_tro_choi') ?></a>
            <?php endif; ?>
        </div>
    </section>
<?php else: ?>
    <section class="panel" aria-labelledby="dl-list-h">
        <header><h2 id="dl-list-h"><?= te('dev.game-downloads.lien_ket_tai_ve') ?></h2><span class="meta"><?= count($links) ?> liên kết</span></header>
        <div class="rows">
            <?php foreach ($links as $l): $lid = (int)$l['ma']; ?>
                <div class="row">
                    <div class="grow">
                        <strong><?= e($l['ten_hien_thi']) ?></strong>
                        <?= status_pill('download_provider', $l['nha_cung_cap']) ?>
                        <?= status_pill('download_platform', $l['he_dieu_ke']) ?>
                        <span class="sub">
                            <?= $l['ten_phien_ban'] ? te('game.version') . ' ' . e($l['ten_phien_ban']) . ' (' . e(label('channel', $l['kenh_phat_hanh'])) . ')' : '' ?>
                            <?= $l['dung_luong'] ? ' ' . te('common.dot') . ' ' . e($l['dung_luong']) : '' ?>
                        </span>
                        <?php if ($l['ghi_chu']): ?><span class="sub"><?= e($l['ghi_chu']) ?></span><?php endif; ?>
                    </div>
                    <a class="btn sm primary" href="<?= e(app_url($base . '/download/' . $lid)) ?>"><?= te('game.downloads.tai_ve') ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="hint"><?= te('game.downloads.lien_ket_mo_o_noi') ?></p>
    </section>
<?php endif; ?>

<?php if ($owned && $releases): ?>
    <section class="panel" style="margin-top: 18px" aria-labelledby="dl-rel-h">
        <header><h2 id="dl-rel-h"><?= te('game.downloads.phien_ban_da_phat_hanh') ?></h2></header>
        <div class="rows">
            <?php foreach ($releases as $r): ?>
                <div class="row">
                    <span class="grow"><?= e($r['ten_phien_ban']) ?></span>
                    <span class="pill"><?= e(label('channel', $r['kenh_phat_hanh'])) ?></span>
                    <span class="sub" style="flex:none"><?= e(fmt_date($r['ngay_phat_hanh'])) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
