<?php
/** Cart. @var array $lines @var ?array $coupon @var array $totals */
?>
<div class="title-row">
    <div>
        <h1><?= te('cart.title') ?></h1>
        <p class="sub"><?= $lines ? fmt_int(count($lines)) . ' ' . te('cart.index.san_pham') : te('cart.index.chua_co_san_pham_nao') ?></p>
    </div>
    <div class="actions"><a class="btn" href="<?= e(app_url('/games')) ?>"><?= te('cart.continue_shopping') ?></a></div>
</div>

<?php if (!$lines): ?>
    <div class="empty">
        <p><?= te('cart.empty') ?></p>
        <a class="btn primary" href="<?= e(app_url('/games')) ?>"><?= te('bundle.index.kham_pha_tro_choi') ?></a>
    </div>
<?php else: ?>
<div class="grid">
    <section class="panel span-8" aria-label="<?= te('cart.index.san_pham_trong_gio') ?>">
        <div class="cart-lines">
            <?php foreach ($lines as $line): ?>
                <article class="cart-line<?= $line['problem'] ? ' is-blocked' : '' ?>">
                    <a class="thumb" href="<?= e($line['url']) ?>">
                        <?php if ($line['cover']): ?><img src="<?= e(app_media_url($line['cover'])) ?>" alt="<?= e($line['cover_alt'] ?: $line['title']) ?>" loading="lazy" data-fallback="<?= e($line['title']) ?>"><?php else: ?><span class="cover-fallback"><?= e($line['title']) ?></span><?php endif; ?>
                    </a>
                    <div class="body">
                        <h2><a href="<?= e($line['url']) ?>"><?= e($line['title']) ?></a></h2>
                        <p class="small muted">
                            <?php if ($line['type'] === 'bundle'): ?>
                                <?= te('cart.index.goi-tro-choi', [ fmt_int($line['game_count']) ,  $line['new_games'] < $line['game_count'] ? ' ' . te('cart.index.dot_ban_da_co') . ' ' . fmt_int($line['game_count'] - $line['new_games']) : '' ]) ?>
                            <?php else: ?>
                                <?= e(label('pricing', $line['pricing'])) ?><?= $line['sale_percent'] > 0 ? ' ' . te('cart.index.dot_dang_giam') . ' ' . e(rtrim(rtrim(number_format($line['sale_percent'], 2), '0'), '.')) . '%' : '' ?>
                            <?php endif; ?>
                        </p>
                        <?php if ($line['problem']): ?><p class="notice small"><?= e($line['problem']) ?></p><?php endif; ?>
                        <?php if ($line['pricing'] === 'pay_what_you_want' && !$line['problem']): ?>
                            <form method="post" action="<?= e(app_url('/cart/add')) ?>" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="type" value="game">
                                <input type="hidden" name="id" value="<?= (int)$line['id'] ?>">
                                <input type="hidden" name="from" value="cart">
                                <label class="small faint" for="amount-<?= (int)$line['id'] ?>"><?= te('cart.index.so_tien_ban_tra') ?></label>
                                <input class="input sm" id="amount-<?= (int)$line['id'] ?>" name="amount" type="number" step="0.01" min="<?= e(number_format($line['min'], 2, '.', '')) ?>" max="1000" value="<?= e(number_format($line['price'], 2, '.', '')) ?>" required>
                                <button class="btn sm" type="submit"><?= te('admin.games.cap_nhat') ?></button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="side">
                        <div class="price">
                            <?php if ($line['sale'] > 0 || $line['coupon'] > 0): ?><span class="was"><?= e(money($line['list'])) ?></span><?php endif; ?>
                            <span><?= e(money($line['net'])) ?></span>
                        </div>
                        <?php if ($line['coupon'] > 0): ?><span class="small faint"><?= te('cart.index.ma-giam', [ e(money($line['coupon'])) ]) ?></span><?php endif; ?>
                        <form method="post" action="<?= e(app_url('/cart/items/' . (int)$line['item_id'] . '/remove')) ?>">
                            <?= csrf_field() ?>
                            <button class="link-btn" type="submit"><?= te('cart.index.bo_khoi_gio') ?></button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <aside class="span-4 stack">
        <section class="panel" aria-labelledby="summary-h">
            <header><h2 id="summary-h"><?= te('cart.index.tom_tat_don_hang') ?></h2></header>
            <dl class="summary">
                <dt><?= te('bundle.original_price') ?></dt><dd><?= e(money($totals['list'])) ?></dd>
                <?php if ($totals['sale'] > 0): ?><dt><?= te('cart.index.giam_gia_chien_dich') ?></dt><dd>−<?= e(money($totals['sale'])) ?></dd><?php endif; ?>
                <?php if ($totals['coupon'] > 0): ?><dt><?= te('cart.index.ma', [ e($coupon['ma_uu_dai']) ]) ?></dt><dd>−<?= e(money($totals['coupon'])) ?></dd><?php endif; ?>
                <dt><?= te('cart.index.thue') ?></dt><dd><?= e(money(0)) ?></dd>
                <dt class="total"><?= te('admin.orders.tong_thanh_toan') ?></dt><dd class="total"><?= e(money($totals['total'])) ?></dd>
            </dl>

            <?php if ($coupon): ?>
                <form method="post" action="<?= e(app_url('/cart/coupon/remove')) ?>" class="split coupon-on">
                    <?= csrf_field() ?>
                    <span class="small"><?= te('cart.index.dang_dung_ma') ?> <strong><?= e($coupon['ma_uu_dai']) ?></strong><?= $totals['coupon_applies'] ? '' : ' (không áp dụng cho sản phẩm hiện tại)' ?></span>
                    <button class="link-btn" type="submit"><?= te('cart.index.go_ma') ?></button>
                </form>
            <?php else: ?>
                <form method="post" action="<?= e(app_url('/cart/coupon')) ?>" class="coupon-form">
                    <?= csrf_field() ?>
                    <label class="sr-only" for="coupon-code"><?= te('cart.coupon') ?></label>
                    <input class="input" id="coupon-code" name="code" maxlength="40" placeholder="<?= te('cart.coupon') ?>" value="<?= e(old('code')) ?>" autocomplete="off">
                    <button class="btn" type="submit"><?= te('cart.apply') ?></button>
                </form>
            <?php endif; ?>

            <?php if ($totals['blocked']): ?>
                <p class="notice small"><?= te('cart.index.co-san-pham-khong-the-thanh-toan-hay-bo-chung-khoi-gio-de-tiep-tuc', [ fmt_int($totals['blocked']) ]) ?></p>
            <?php else: ?>
                <form method="post" action="<?= e(app_url('/cart/checkout')) ?>" class="form checkout-form">
                    <?= csrf_field() ?>
                    <label class="check">
                        <input type="checkbox" name="confirm_sandbox" value="1" required>
                        <span><?= te('cart.index.toi_hieu_day_la') ?> <strong><?= te('cart.index.thanh_toan_thu_nghiem_sandbox') ?></strong><span class="sub"><?= te('cart.index.khong_co_tien_that_duoc') ?></span></span>
                    </label>
                    <button class="btn primary block" type="submit"><?= te('cart.index.thanh-toan-thu', [ e(money($totals['total'])) ]) ?></button>
                </form>
            <?php endif; ?>
        </section>
        <p class="small faint"><?= te('cart.index.ban-co-the-yeu-cau-hoan-tien-trong-ngay-ke-tu-khi-thanh-toan-tai-trang-chi-tiet-don-hang', [ (int)REFUND_WINDOW_DAYS ]) ?></p>
    </aside>
</div>
<?php endif; ?>
