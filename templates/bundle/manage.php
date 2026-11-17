<?php
/** Bundle list for developers (own bundles) and administrators (all). @var string $scope @var array $bundles @var int $gameCount */
$isAdmin = $scope === 'admin';
$base = $isAdmin ? '/admin/bundles' : '/dev/bundles';
?>
<div class="title-row">
    <div>
        <h1><?= $isAdmin ? te('nav.bundles') : te('nav.my_bundles') ?></h1>
        <p class="sub"><?= $isAdmin
            ? te('bundle.manage.moi_goi_tren_cua_hang')
            : te('bundle.manage.goi_gom_cac_tro_choi') ?></p>
    </div>
    <a class="btn primary" href="<?= e(app_url($base . '/new')) ?>"><?= te('bundle.manage.tao_goi') ?></a>
</div>

<?php if (!$isAdmin && $gameCount < 2): ?>
    <p class="hint">Cần ít nhất 2 trò chơi đã công bố mà bạn là tác giả để tạo gói. Hiện bạn có <?= fmt_int($gameCount) ?>.</p>
<?php endif; ?>

<?php if (!$bundles): ?>
    <div class="empty"><p><?= te('bundle.empty') ?></p><a class="btn primary" href="<?= e(app_url($base . '/new')) ?>"><?= te('bundle.manage.tao_goi_dau_tien') ?></a></div>
<?php else: ?>
<section class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th scope="col"><?= te('bundle.manage.goi') ?></th><th scope="col" class="num"><?= te('common.price') ?></th><th scope="col" class="num"><?= te('admin.games') ?></th><th scope="col" class="num"><?= te('bundle.manage.da_ban') ?></th>
                <th scope="col"><?= te('bundle.manage.thoi_gian_ban') ?></th><th scope="col"><?= te('common.status') ?></th><th scope="col"><span class="sr-only"><?= te('common.actions') ?></span></th>
            </tr></thead>
            <tbody>
            <?php foreach ($bundles as $b): $id = (int)$b['ma']; ?>
                <tr>
                    <td class="lead"><a class="link" href="<?= e(app_url($base . '/' . $id)) ?>"><?= e($b['tieu_de']) ?></a>
                        <span class="sub"><?= e(label('bundle_type', $b['loai_goi_game'])) ?><?= $isAdmin ? ' ' . te('common.dot') . ' ' . e($b['nguoi_tao']) : '' ?></span></td>
                    <td class="num"><?= (float)$b['gia'] > 0 ? e(money((float)$b['gia'])) : te('price.free') ?></td>
                    <td class="num"><?= fmt_int((int)$b['so_game']) ?></td>
                    <td class="num"><?= fmt_int((int)$b['so_luot_ban']) ?></td>
                    <td class="when"><?php if (!$b['ngay_bat_dau'] && !$b['ngay_ket_thuc']): ?><span class="muted"><?= te('bundle.manage.khong_gioi_han') ?></span><?php else: ?>
                        <?= $b['ngay_bat_dau'] ? te('bundle.manage.tu') . ' ' . e(fmt_datetime($b['ngay_bat_dau'])) : te('bundle.manage.tu_khi_cong_bo') ?><span class="sub"><?= $b['ngay_ket_thuc'] ? te('bundle.manage.den') . ' ' . e(fmt_datetime($b['ngay_ket_thuc'])) : te('bundle.manage.khong_het_han') ?></span>
                    <?php endif; ?></td>
                    <td><?= status_pill('bundle_status', $b['trang_thai']) ?><?php if ($b['trang_thai'] === 'published' && !BundleModel::isOnSale($b)): ?><span class="sub"><?= te('bundle.manage.ngoai_thoi_gian_ban') ?></span><?php endif; ?></td>
                    <td class="actions"><a class="link" href="<?= e(app_url('/bundles/' . rawurlencode($b['duong_dan_rut_gon']))) ?>">Xem trang<span class="sr-only"> <?= e($b['tieu_de']) ?></span></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
</section>
<?php endif; ?>
