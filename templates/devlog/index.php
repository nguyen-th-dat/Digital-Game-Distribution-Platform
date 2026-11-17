<?php
/** Devlog list for one game. @var array $game @var array $posts @var int $page @var int $pages @var int $total */
$base = '/games/' . rawurlencode($game['duong_dan_rut_gon']);
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url($base)) ?>"><?= e($game['tieu_de']) ?></a><span><?= te('devlog.title') ?></span></nav>
<div class="title-row">
    <div>
        <h1><?= te('devlog.title') ?></h1>
        <p class="sub"><?= te('devlog.index.bai-ve-theo-doi-tro-choi-de-nhan-thong-bao-khi-co-bai-moi', [ fmt_int($total) ,  e($game['tieu_de']) ]) ?></p>
    </div>
</div>

<?php if (!$posts): ?>
    <div class="empty"><p><?= te('devlog.index.nha_phat_trien_chua_dang') ?></p><a class="btn" href="<?= e(app_url($base)) ?>"><?= te('devlog.index.ve_trang_tro_choi') ?></a></div>
<?php else: ?>
<section class="panel">
    <div class="rows">
        <?php foreach ($posts as $p): ?>
            <a class="row row-link devlog-row" href="<?= e(app_url($base . '/devlogs/' . rawurlencode($p['duong_dan_rut_gon']))) ?>">
                <span class="grow">
                    <strong><?= e($p['tieu_de']) ?></strong>
                    <span class="sub"><?= e(excerpt($p['noi_dung'], 220)) ?></span>
                </span>
                <span class="small faint"><?= e(fmt_date($p['ngay_cong_bo'])) ?><br><?= te('devlog.index.binh-luan', [ fmt_int((int)$p['so_binh_luan']) ]) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
</section>
<?php endif; ?>
