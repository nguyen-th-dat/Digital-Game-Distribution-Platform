<?php
/** Jam list. @var array $tabs @var string $tab @var array $counts @var array $jams */
$emptyText = [
    'open' => te('jam.index.hien_chua_co_cuoc_thi'),
    'judging' => te('jam.index.khong_co_cuoc_thi_nao'),
    'past' => te('jam.index.chua_co_cuoc_thi_nao'),
    'hidden' => te('jam.index.khong_co_cuoc_thi_nhap'),
];
?>
<div class="title-row">
    <div>
        <h1><?= te('jam.index.cuoc_thi_lam_game') ?></h1>
        <p class="sub"><?= te('jam.index.lam_mot_tro_choi_trong') ?></p>
    </div>
    <?php if (is_staff()): ?><a class="btn" href="<?= e(app_url('/admin/jams/new')) ?>"><?= te('admin.jams.tao_cuoc_thi') ?></a><?php endif; ?>
</div>

<nav class="tabs" aria-label="<?= te('jam.index.loc_cuoc_thi') ?>">
    <?php foreach ($tabs as $key => $text): ?>
        <a href="<?= e(app_url('/jams' . ($key === 'open' ? '' : '?tab=' . $key))) ?>" aria-selected="<?= $tab === $key ? 'true' : 'false' ?>"><?= e($text) ?><span class="count"><?= fmt_int($counts[$key] ?? 0) ?></span></a>
    <?php endforeach; ?>
</nav>

<?php if ($jams): ?>
    <div class="jam-list">
        <?php foreach ($jams as $j):
            $phase = JamModel::phase($j);
            $url = app_url('/jams/' . rawurlencode($j['duong_dan_rut_gon']));
            ?>
            <a class="jam-card" href="<?= e($url) ?>">
                <div class="grow">
                    <h2><?= e($j['tieu_de']) ?></h2>
                    <p class="sub">Tổ chức bởi <?= e($j['ten_hien_thi']) ?></p>
                    <?php if ($j['mo_ta']): ?><p class="blurb"><?= e(excerpt($j['mo_ta'], 180)) ?></p><?php endif; ?>
                </div>
                <dl class="jam-dates">
                    <dt><?= te('common.status') ?></dt><dd><span class="pill<?= $phase === 'submissions' || $phase === 'voting' ? ' pos' : ($phase === 'cancelled' ? ' neg' : '') ?>"><?= e(JamModel::PHASES[$phase]) ?></span></dd>
                    <?php if ($phase === 'upcoming'): ?><dt><?= te('admin.campaign-form.bat_dau') ?></dt><dd><?= e(fmt_datetime($j['ngay_bat_dau'])) ?></dd>
                    <?php elseif ($phase === 'submissions'): ?><dt><?= te('admin.jams.han_nop') ?></dt><dd><?= e(fmt_datetime($j['han_nop_bai'])) ?></dd>
                    <?php elseif ($phase === 'voting' && $j['ngay_ket_thuc_binh_chon']): ?><dt><?= te('jam.index.het_binh_chon') ?></dt><dd><?= e(fmt_datetime($j['ngay_ket_thuc_binh_chon'])) ?></dd>
                    <?php else: ?><dt><?= te('admin.jams.han_nop') ?></dt><dd><?= e(fmt_date($j['han_nop_bai'])) ?></dd><?php endif; ?>
                    <dt><?= te('jam.entries') ?></dt><dd class="num"><?= fmt_int((int)$j['so_bai']) ?></dd>
                </dl>
            </a>
        <?php endforeach; ?>
    </div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
<?php else: ?>
    <div class="empty"><p><?= e($emptyText[$tab]) ?></p><?php if ($tab !== 'past'): ?><a class="btn" href="<?= e(app_url('/jams?tab=past')) ?>"><?= te('jam.index.xem_cuoc_thi_da_ket') ?></a><?php endif; ?></div>
<?php endif; ?>
