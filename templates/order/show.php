<?php
/** Order detail. @var array $order @var array $items @var array $payments @var array $refunds @var ?array $coupon @var bool $mine */
$cur = $order['ma_tien_te'];
$isSandbox = str_starts_with($order['ma_don_hang'], 'TEST-');
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>">
    <a href="<?= e(app_url('/orders')) ?>"><?= te('admin.orders') ?></a><span><?= e($order['ma_don_hang']) ?></span>
</nav>

<div class="object-head">
    <div class="grow">
        <h1><?= e($order['ma_don_hang']) ?></h1>
        <p class="meta">
            <?= te('order.show.dat-luc', [ e(fmt_datetime($order['ngay_dat_hang'])) ]) ?>
            <?php if (!$mine): ?> <?= te('order.show.nguoi-mua') ?> <a class="link" href="<?= e(app_url('/admin/users/' . (int)$order['nguoi_mua_id'])) ?>"><?= e($order['buyer_name']) ?></a><?php endif; ?>
        </p>
    </div>
    <?= status_pill('order_status', $order['trang_thai']) ?>
</div>

<?php if ($isSandbox): ?>
    <p class="notice neutral"><?= te('order.show.don_hang_thu_nghiem_sandbox') ?></p>
<?php endif; ?>

<div class="grid">
    <section class="panel span-8" aria-labelledby="items-h">
        <header><h2 id="items-h"><?= te('order.items') ?></h2></header>
        <div class="rows">
            <?php foreach ($items as $item): ?>
                <?php
                $href = $item['game_slug'] ? app_url('/games/' . rawurlencode($item['game_slug'])) : ($item['bundle_slug'] ? app_url('/bundles/' . rawurlencode($item['bundle_slug'])) : null);
                ?>
                <div class="row order-item" id="item-<?= (int)$item['ma'] ?>">
                    <div class="grow">
                        <?php if ($href): ?><a class="link" href="<?= e($href) ?>"><?= e($item['tieu_de_luc_mua']) ?></a><?php else: ?><?= e($item['tieu_de_luc_mua']) ?><?php endif; ?>
                        <span class="sub">
                            <?= te('order.show.gia', [ $item['loai_muc'] === 'bundle' ? te('nav.bundles') : te('profile.games') ,  e(money($item['don_gia'], $cur)) ]) ?>
                            <?php if ((float)$item['so_tien_giam'] > 0): ?> <?= te('order.show.giam', [ e(money($item['so_tien_giam'], $cur)) ]) ?><?php endif; ?>
                        </span>
                        <?php if ($item['refund_status']): ?>
                            <span class="sub"><?= te('order.show.hoan-tien', [ e(money($item['refund_amount'], $cur)) ,  status_pill('refund_status', $item['refund_status']) ]) ?></span>
                        <?php elseif ($mine && !$item['refund_problem']): ?>
                            <details class="refund-form">
                                <summary class="link-btn"><?= te('order.request_refund') ?></summary>
                                <form method="post" action="<?= e(app_url('/orders/' . rawurlencode($order['ma_don_hang']) . '/refund')) ?>" class="form well">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= (int)$item['ma'] ?>">
                                    <div class="field">
                                        <label for="reason-<?= (int)$item['ma'] ?>"><?= te('admin.reason') ?></label>
                                        <textarea class="input" id="reason-<?= (int)$item['ma'] ?>" name="ly_do" rows="3" minlength="10" maxlength="2000" required><?= e(old('ly_do')) ?></textarea>
                                        <span class="hint"><?= te('order.show.tu_10_den_2_000') ?></span>
                                    </div>
                                    <div class="form-actions"><button class="btn sm primary" type="submit"><?= te('order.show.gui-yeu-cau', [ e(money($item['thanh_tien_dong'], $cur)) ]) ?></button></div>
                                </form>
                            </details>
                        <?php endif; ?>
                    </div>
                    <span class="price"><?= e(money($item['thanh_tien_dong'], $cur)) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <aside class="span-4 stack">
        <section class="panel" aria-labelledby="sum-h">
            <header><h2 id="sum-h"><?= te('cart.checkout') ?></h2></header>
            <dl class="summary">
                <dt><?= te('order.show.tien_hang') ?></dt><dd><?= e(money($order['tong_tien_hang'], $cur)) ?></dd>
                <?php if ((float)$order['tong_tien_giam'] > 0): ?><dt><?= te('order.show.giam-gia', [ $coupon ? ' ' . '(' . te('order.show.gom_ma') . ' ' . e($coupon['ma_uu_dai']) . ')' : '' ]) ?></dt><dd>−<?= e(money($order['tong_tien_giam'], $cur)) ?></dd><?php endif; ?>
                <dt><?= te('cart.index.thue') ?></dt><dd><?= e(money($order['tong_tien_thue'], $cur)) ?></dd>
                <dt class="total"><?= te('common.total') ?></dt><dd class="total"><?= e(money($order['tong_thanh_toan'], $cur)) ?></dd>
            </dl>
            <?php if ($payments): ?>
                <div class="rows">
                    <?php foreach ($payments as $p): ?>
                        <div class="row">
                            <div class="grow"><?= e($p['nha_cung_cap'] === 'sandbox' ? te('order.show.sandbox_thu_nghiem') : $p['nha_cung_cap']) ?><span class="sub"><?= e($p['ma_giao_dich_nha_cung_cap'] ?? te('common.emdash')) ?> · <?= e(fmt_datetime($p['ngay_thanh_toan'] ?? $p['ngay_tao'])) ?></span></div>
                            <?= status_pill('order_status', $p['trang_thai'] === 'succeeded' ? 'paid' : $p['trang_thai']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="small faint"><?= te('order.show.khong_co_giao_dich_thanh') ?></p>
            <?php endif; ?>
        </section>

        <?php if ($refunds): ?>
        <section class="panel" aria-labelledby="ref-h">
            <header><h2 id="ref-h"><?= te('admin.refunds') ?></h2></header>
            <div class="rows">
                <?php foreach ($refunds as $r): ?>
                    <div class="row">
                        <div class="grow"><?= e($r['tieu_de_luc_mua'] ?? te('order.show.toan_don')) ?><span class="sub"><?= e(fmt_datetime($r['ngay_tao'])) ?><?= $r['ngay_xu_ly'] ? ' ' . te('common.dot_xu_ly') . ' ' . e(fmt_datetime($r['ngay_xu_ly'])) : '' ?></span></div>
                        <div class="stack-tight"><span class="price"><?= e(money($r['so_tien'], $cur)) ?></span><?= status_pill('refund_status', $r['trang_thai']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        <p class="small faint"><?= te('order.show.hoan-tien-duoc-chap-nhan-trong-ngay-ke-tu-khi-thanh-toan-cho-tung-san-pham-co-gia-tren-0', [ (int)REFUND_WINDOW_DAYS ]) ?></p>
    </aside>
</div>
