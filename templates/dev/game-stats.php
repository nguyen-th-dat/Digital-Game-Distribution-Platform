<?php
/** Project statistics. @var array $game @var string $tab @var array $series @var array $totals @var int $days @var array $lifetime */
$gid = (int)$game['ma'];
$base = '/dev/games/' . $gid . '/stats';
$fmt = static fn(string $col, $v) => $col === 'doanh_thu_gop' ? money((float)$v) : fmt_int((int)$v);
$day = static fn(string $ymd) => date('d/m', strtotime($ymd));
// One chart per measure: the scales differ too much to share an axis.
$charts = [
    'luot_xem_trang'      => te('dev.overview.luot_xem_trang'),
    'luot_nhan_tai_xuong' => te('dev.overview.luot_tai_xuong'),
    'so_luot_mua'         => te('dev.overview.luot_mua'),
    'doanh_thu_gop'       => te('dev.overview.doanh_thu_gop'),
];
/** Smallest 1, 2 or 5 times a power of ten that is at least $v. */
$niceMax = static function (float $v): float {
    if ($v <= 0) return 1;
    $p = 10 ** floor(log10($v));
    foreach ([1, 2, 5, 10] as $m) {
        if ($m * $p >= $v) return $m * $p;
    }
    return 10 * $p;
};
$n = count($series);
$hasData = array_sum(array_map('floatval', $totals)) > 0;
?>
<?php partial('dev-game-tabs', ['game' => $game, 'tab' => $tab]); ?>

<nav class="chips chips-row" aria-label="<?= te('dev.game-stats.khoang_thoi_gian') ?>">
    <?php foreach ([7, 30, 90] as $d): ?>
        <a class="chip"<?= $days === $d ? ' aria-current="true"' : '' ?> href="<?= e(app_url($base . qs(['days' => $d === 30 ? null : $d]))) ?>"><?= te('dev.game-stats.ngay', [ $d ]) ?></a>
    <?php endforeach; ?>
</nav>

<section class="kpis section" aria-label="<?= te('dev.game-stats.tong-days-ngay') ?>">
    <?php foreach ($charts as $col => $name): ?>
        <div class="kpi"><div class="kpi-label"><?= e($name) ?></div><div class="kpi-value"><?= e($fmt($col, $totals[$col])) ?></div><div class="delta"><?= te('dev.game-stats.ngay-qua', [ $days ]) ?></div></div>
    <?php endforeach; ?>
</section>

<?php if (!$hasData): ?>
    <div class="empty section"><p><?= te('dev.game-stats.chua-co-so-lieu-trong-ngay-qua-luot-xem-chi-duoc-ghi-khi-du-an-cong-khai-hoac-khong-niem-yet-moi-phien-trinh-duyet-mot-lan-moi-ngay', [ $days ]) ?></p></div>
<?php else: ?>
<div class="chart-grid section">
    <?php foreach ($charts as $col => $name):
        $values = array_map(static fn($r) => (float)$r[$col], $series);
        $top = $niceMax(max($values));
        $peak = max($values);
    ?>
        <section class="panel" aria-labelledby="ch-<?= e($col) ?>">
            <header><h2 id="ch-<?= e($col) ?>"><?= te('dev.game-stats.theo-ngay', [ e($name) ]) ?></h2><?php if ($peak > 0): ?><span class="meta"><?= te('dev.game-stats.cao-nhat', [ e($fmt($col, $peak)) ]) ?></span><?php endif; ?></header>
            <?php if ($peak <= 0): ?>
            <p class="muted"><?= te('dev.game-stats.chua-co-so-lieu-trong-ngay-qua', [ $days ]) ?></p>
            <?php else: ?>
            <div class="chart" role="img" aria-label="<?= te('dev.game-stats.e-name-theo-ngay-days-ngay-qua-tong-e-fmt-col-totals-col-cao-nhat-e-fmt-col-peak-trong-mot-ngay-so-lieu-tung-ngay-co-trong-bang-ben-duoi') ?>">
                <div class="chart-y" aria-hidden="true"><span><?= e($fmt($col, $top)) ?></span><span>0</span></div>
                <div>
                    <div class="bars" style="--n: <?= $n ?>">
                        <?php foreach ($series as $i => $r): $val = (float)$r[$col]; $side = $i < $n / 4 ? ' tip-l' : ($i >= $n * 3 / 4 ? ' tip-r' : ''); ?>
                            <div class="col<?= $side ?>" data-tip="<?= e($fmt($col, $val) . ' ' . te('common.dot') . ' ' . fmt_date($r['ngay'])) ?>"><?php if ($val > 0): ?><span class="bar" style="height: <?= round($val / $top * 100, 2) ?>%"></span><?php endif; ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="bars-axis" aria-hidden="true"><span><?= e($day($series[0]['ngay'])) ?></span><span><?= e($day($series[intdiv($n, 2)]['ngay'])) ?></span><span><?= e($day($series[$n - 1]['ngay'])) ?></span></div>
                </div>
            </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>

<details class="panel section">
    <summary class="link-btn"><?= te('dev.game-stats.xem_so_lieu_dang_bang') ?></summary>
    <div class="table-wrap">
        <table>
            <thead><tr><th scope="col"><?= te('common.date') ?></th><?php foreach ($charts as $name): ?><th scope="col" class="num"><?= e($name) ?></th><?php endforeach; ?><th scope="col" class="num"><?= te('dev.game-stats.them_yeu_thich') ?></th><th scope="col" class="num"><?= te('devlog.comments') ?></th></tr></thead>
            <tbody>
            <?php foreach (array_reverse($series) as $r): ?>
                <tr>
                    <td><?= e(fmt_date($r['ngay'])) ?></td>
                    <?php foreach (array_keys($charts) as $col): ?><td class="num"><?= e($fmt($col, $r[$col])) ?></td><?php endforeach; ?>
                    <td class="num"><?= fmt_int((int)$r['luot_them_yeu_thich']) ?></td>
                    <td class="num"><?= fmt_int((int)$r['so_luong_binh_luan']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</details>
<?php endif; ?>

<section class="panel section" aria-labelledby="life-h">
    <header><h2 id="life-h"><?= te('dev.game-stats.tu_truoc_den_nay') ?></h2></header>
    <dl class="facts">
        <dt><?= te('dev.game-stats.nguoi_so_huu') ?></dt><dd><?= fmt_int((int)$lifetime['so_huu']) ?></dd>
        <dt><?= te('dev.game-stats.trong_danh_sach_yeu_thich') ?></dt><dd><?= fmt_int((int)$lifetime['yeu_thich']) ?></dd>
        <dt><?= te('profile.followers') ?></dt><dd><?= fmt_int((int)$lifetime['theo_doi']) ?></dd>
        <dt><?= te('game.reviews') ?></dt><dd><?= fmt_int((int)$lifetime['so_danh_gia']) ?><?= $lifetime['diem_tb'] !== null ? ' ' . te('dev.game-stats.dot_trung_binh') . ' ' . e(number_format((float)$lifetime['diem_tb'], 1, ',', '')) . '/5' : '' ?></dd>
        <dt><?= te('dev.game-stats.them-yeu-thich-ngay', [ $days ]) ?></dt><dd><?= fmt_int((int)$totals['luot_them_yeu_thich']) ?></dd>
        <dt><?= te('dev.game-stats.binh-luan-moi-ngay', [ $days ]) ?></dt><dd><?= fmt_int((int)$totals['so_luong_binh_luan']) ?></dd>
    </dl>
</section>
