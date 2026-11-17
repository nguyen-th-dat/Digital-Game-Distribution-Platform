<?php
/** Admin order list. @var array $filters @var array $counts @var array $totals @var array $orders @var int $total @var int $page @var int $pages */
$all = array_sum(array_map('intval', $counts));
?>
<div class="title-row">
    <div><h1><?= te('admin.orders') ?></h1><p class="sub"><?= te('admin.orders.tra_cuu_don_theo_ma') ?></p></div>
    <a class="btn" href="<?= e(app_url('/admin/refunds')) ?>"><?= te('order.request_refund') ?></a>
</div>

<section class="kpis" aria-label="<?= te('admin.orders.tong_quan_doanh_thu') ?>">
    <div class="kpi"><div class="kpi-label"><?= te('admin.orders.tong_so_don') ?></div><div class="kpi-value"><?= fmt_int($all) ?></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('admin.orders.doanh_thu_da_thanh_toan') ?></div><div class="kpi-value"><?= e(money($totals['gross'])) ?></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('label.order_status.refunded') ?></div><div class="kpi-value"><?= e(money($totals['refunded'])) ?></div></div>
</section>

<nav class="chips chips-row section" aria-label="<?= te('admin.games.loc_theo_trang_thai') ?>">
    <a class="chip"<?= $filters['status'] === '' ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/orders' . qs(['status' => null, 'page' => null]))) ?>"><?= te('common.all') ?> <span class="muted"><?= fmt_int($all) ?></span></a>
    <?php foreach (labels('order_status') as $k => $t): if (empty($counts[$k])) continue; ?>
        <a class="chip"<?= $filters['status'] === $k ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/orders' . qs(['status' => $k, 'page' => null]))) ?>"><?= e($t) ?> <span class="muted"><?= fmt_int((int)$counts[$k]) ?></span></a>
    <?php endforeach; ?>
</nav>

<form class="toolbar" method="get" action="<?= e(app_url('/admin/orders')) ?>">
    <?php if ($filters['status'] !== ''): ?><input type="hidden" name="status" value="<?= e($filters['status']) ?>"><?php endif; ?>
    <div class="search">
        <label class="sr-only" for="o-q"><?= te('admin.orders.tim_theo_ma_don_ten') ?></label>
        <input class="input" id="o-q" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="<?= te('admin.orders.ma_don_ten_dang_nhap') ?>" maxlength="100">
    </div>
    <span class="spacer"></span>
    <span class="count"><?= te('admin.orders.don', [ fmt_int($total) ]) ?></span>
</form>

<?php if ($orders): ?>
<section class="panel flush">
    <table>
        <thead><tr><th scope="col"><?= te('admin.orders') ?></th><th scope="col"><?= te('admin.orders.nguoi_mua') ?></th><th scope="col"><?= te('common.status') ?></th><th scope="col" class="num"><?= te('admin.orders.tong_thanh_toan') ?></th><th scope="col"><?= te('order.date') ?></th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>
                    <details class="edit">
                        <summary class="link-btn"><?= e($o['ma_don_hang']) ?></summary>
                        <div class="well">
                            <div class="rows">
                                <?php foreach ($o['items'] as $i): ?>
                                    <div class="row">
                                        <div class="grow"><?= e($i['tieu_de_luc_mua']) ?><span class="sub"><?= $i['loai_muc'] === 'bundle' ? te('nav.bundles') : te('profile.games') ?> · <?= e(money($i['don_gia'])) ?><?= (float)$i['so_tien_giam'] > 0 ? ' ' . te('admin.orders.dot_giam') . ' ' . e(money($i['so_tien_giam'])) : '' ?></span></div>
                                        <?php if ($i['refund_status']): ?><?= status_pill('refund_status', $i['refund_status']) ?><?php endif; ?>
                                        <span><?= e(money($i['thanh_tien_dong'])) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php foreach ($o['payments'] as $p): ?>
                                <p class="small muted"><?= te('admin.orders.thanh-toan-qua', [ (int)$p['ma'] ,  e($p['nha_cung_cap']) ,  e(money($p['so_tien'])) ,  e(label('payment_status', $p['trang_thai'])) ,  $p['ngay_thanh_toan'] ? ' ' . te('common.dot') . ' ' . e(fmt_datetime($p['ngay_thanh_toan'])) : '' ]) ?></p>
                            <?php endforeach; ?>
                        </div>
                    </details>
                    <span class="sub"><?= te('admin.orders.muc', [ fmt_int((int)$o['item_count']) ]) ?></span>
                </td>
                <td><a class="link" href="<?= e(app_url('/admin/users/' . (int)$o['nguoi_mua_id'])) ?>"><?= e($o['buyer_name']) ?></a><span class="sub">@<?= e($o['buyer_username']) ?></span></td>
                <td><?= status_pill('order_status', $o['trang_thai']) ?></td>
                <td class="num"><?= e(money($o['tong_thanh_toan'])) ?><?php if ((float)$o['tong_tien_giam'] > 0): ?><span class="sub"><?= te('admin.orders.giam', [ e(money($o['tong_tien_giam'])) ]) ?></span><?php endif; ?></td>
                <td><?= e(fmt_datetime($o['ngay_dat_hang'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
<?php else: ?>
    <div class="empty"><p><?= te('admin.orders.khong_co_don_hang_nao') ?></p><?php if ($filters['q'] !== '' || $filters['status'] !== ''): ?><a class="btn" href="<?= e(app_url('/admin/orders')) ?>"><?= te('game.clear_filters') ?></a><?php endif; ?></div>
<?php endif; ?>
