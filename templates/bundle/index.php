<?php
/** Bundles on sale. @var array $bundles */
?>
<div class="title-row">
    <div>
        <h1><?= te('bundle.bundles') ?></h1>
        <p class="sub"><?= te('bundle.index.nhieu_tro_choi_mot_muc') ?></p>
    </div>
</div>

<?php if (!$bundles): ?>
    <div class="empty"><p><?= te('bundle.index.hien_chua_co_goi_game') ?></p><a class="btn" href="<?= e(app_url('/games')) ?>"><?= te('bundle.index.kham_pha_tro_choi') ?></a></div>
<?php else: ?>
<div class="games-grid">
    <?php foreach ($bundles as $b):
        $list = (float)$b['tong_gia_le'];
        $price = (float)$b['gia'];
        $cover = app_media_url((string)($b['anh_bia'] ?? ''));
        ?>
        <a class="game-card" href="<?= e(app_url('/bundles/' . rawurlencode($b['duong_dan_rut_gon']))) ?>">
            <div class="cover">
                <?php if ($cover !== ''): ?><img src="<?= e($cover) ?>" alt="" loading="lazy" data-fallback="<?= e($b['tieu_de']) ?>"><?php else: ?><div class="cover-fallback"><?= e($b['tieu_de']) ?></div><?php endif; ?>
            </div>
            <h3><?= e($b['tieu_de']) ?></h3>
            <div class="meta-line">
                <span class="by"><?= fmt_int((int)$b['so_game']) ?> trò chơi · <?= e($b['nguoi_tao']) ?></span>
                <span class="price"><?php if ($list > $price): ?><span class="was"><?= e(money($list)) ?></span><?php endif; ?><span><?= e($price > 0 ? money($price) : te('price.free')) ?></span></span>
            </div>
            <?php if ($b['ngay_ket_thuc']): ?><p class="blurb">Kết thúc <?= e(fmt_date($b['ngay_ket_thuc'])) ?></p><?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>
<?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
<?php endif; ?>
