<?php
/** Jam admin list. @var array $jams */
?>
<div class="title-row">
    <div><h1>Game jam</h1><p class="sub"><?= te('admin.jams.tao_cuoc_thi_dat_lich') ?></p></div>
    <a class="btn primary" href="<?= e(app_url('/admin/jams/new')) ?>"><?= te('admin.jams.tao_cuoc_thi') ?></a>
</div>

<?php if ($jams): ?>
<section class="panel">
    <div class="table-wrap"><table>
        <thead><tr><th scope="col"><?= te('admin.jams.cuoc_thi') ?></th><th scope="col"><?= te('common.status') ?></th><th scope="col"><?= te('admin.jams.giai_doan_hien_tai') ?></th><th scope="col"><?= te('admin.jams.han_nop') ?></th><th scope="col" class="num"><?= te('admin.jams.bai') ?></th><th scope="col" class="num"><?= te('admin.jams.nguoi_cham') ?></th></tr></thead>
        <tbody>
        <?php foreach ($jams as $j): $phase = JamModel::phase($j); ?>
            <tr>
                <td class="lead"><a class="link" href="<?= e(app_url('/admin/jams/' . (int)$j['ma'])) ?>"><?= e($j['tieu_de']) ?></a><span class="sub">/jams/<?= e($j['duong_dan_rut_gon']) ?> · <?= e($j['ten_hien_thi']) ?></span></td>
                <td><?= e(label('jam_status', $j['trang_thai'])) ?></td>
                <td><span class="pill<?= in_array($phase, ['submissions', 'voting'], true) ? ' pos' : ($phase === 'cancelled' ? ' neg' : '') ?>"><?= e(JamModel::PHASES[$phase]) ?></span></td>
                <td><?= e(fmt_datetime($j['han_nop_bai'])) ?></td>
                <td class="num"><?= fmt_int((int)$j['so_bai']) ?></td>
                <td class="num"><?= fmt_int((int)$j['so_nguoi_cham']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
</section>
<?php else: ?>
    <div class="empty"><p><?= te('admin.jams.chua_co_cuoc_thi_nao') ?></p><a class="btn primary" href="<?= e(app_url('/admin/jams/new')) ?>"><?= te('admin.jams.tao_cuoc_thi_dau_tien') ?></a></div>
<?php endif; ?>
