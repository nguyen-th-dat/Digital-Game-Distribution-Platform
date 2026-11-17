<?php
/** Buyer's order history. @var array $orders @var string $status */
$tabs = ['' => t('common.all'), 'paid' => labels('order_status')['paid'], 'partially_refunded' => labels('order_status')['partially_refunded'], 'refunded' => labels('order_status')['refunded']];
?>
<div class="title-row">
    <div>
        <h1><?= te('admin.orders') ?></h1>
        <p class="sub"><?= te('order.index.lich_su_mua_hang_moi') ?></p>
    </div>
</div>

<nav class="tabs" aria-label="<?= te('admin.games.loc_theo_trang_thai') ?>">
    <?php foreach ($tabs as $value => $text): ?>
        <a href="<?= e(app_url('/orders' . ($value !== '' ? '?status=' . $value : ''))) ?>" aria-selected="<?= $status === $value ? 'true' : 'false' ?>"><?= e($text) ?></a>
    <?php endforeach; ?>
</nav>

<?php if (!$orders): ?>
    <div class="empty">
        <p><?= $status === '' ? te('order.index.ban.chua.co.don.hang.nao') : te('order.index.khong.co.don.hang.o.trang.thai.nay') ?></p>
        <a class="btn primary" href="<?= e(app_url('/games')) ?>"><?= te('bundle.index.kham_pha_tro_choi') ?></a>
    </div>
<?php else: ?>
<section class="panel flush">
    <div class="table-wrap">
        <table class="stack-sm">
            <thead><tr><th><?= te('order.code') ?></th><th><?= te('order.items') ?></th><th><?= te('order.date') ?></th><th><?= te('common.status') ?></th><th class="n"><?= te('common.total') ?></th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><a class="link" href="<?= e(app_url('/orders/' . rawurlencode($order['ma_don_hang']))) ?>"><?= e($order['ma_don_hang']) ?></a></td>
                    <td data-label="<?= te('order.index.san-pham') ?>"><span class="truncate"><?= e($order['titles'] ?: te('common.emdash')) ?></span></td>
                    <td data-label="<?= te('order.index.ngay') ?>" class="hide-sm"><?= e(fmt_datetime($order['ngay_dat_hang'])) ?></td>
                    <td data-label="<?= te('order.index.trang-thai') ?>"><?= status_pill('order_status', $order['trang_thai']) ?></td>
                    <td class="n"><?= e(money($order['tong_thanh_toan'], $order['ma_tien_te'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
</section>
<?php endif; ?>
