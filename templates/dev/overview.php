<?php
/** Developer overview. @var array $balances @var array $games @var array $traffic @var array $recent @var bool $hasAccount */
?>
<div class="title-row">
    <div><h1><?= te('dev.overview.tong_quan_nha_phat_trien') ?></h1><p class="sub"><?= te('dev.overview.doanh_thu_luu_luong_30') ?></p></div>
    <a class="btn primary" href="<?= e(app_url('/dev/games/new')) ?>"><?= te('dev.create_game') ?></a>
</div>

<section class="kpis" aria-label="<?= te('dev.overview.so_du') ?>">
    <div class="kpi"><div class="kpi-label"><?= te('label.ledger_status.available') ?></div><div class="kpi-value"><?= e(money($balances['available'])) ?></div><div class="delta"><a class="link" href="<?= e(app_url('/dev/payouts')) ?>"><?= $hasAccount ? te('dev.request_payout') : te('dev.overview.them_tai_khoan_nhan_tien') ?></a></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('label.ledger_status.pending') ?></div><div class="kpi-value"><?= e(money($balances['pending'])) ?></div><div class="delta">Giữ <?= (int)PAYOUT_HOLD_DAYS ?> ngày để xử lý hoàn tiền</div></div>
    <div class="kpi"><div class="kpi-label"><?= te('label.ledger_status.paid') ?></div><div class="kpi-value"><?= e(money($balances['paid'])) ?></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('dev.overview.doanh_thu_gop') ?></div><div class="kpi-value"><?= e(money($balances['gross'])) ?></div><div class="delta">Phí nền tảng <?= e(money($balances['fee'])) ?></div></div>
</section>

<div class="grid top section">
    <section class="panel span-8" aria-labelledby="games-h">
        <header><h2 id="games-h"><?= te('dev.games') ?></h2><span class="meta"><a class="link" href="<?= e(app_url('/dev/games')) ?>"><?= te('dev.overview.quan_ly_tat_ca') ?></a></span></header>
        <?php if (!$games): ?>
            <div class="empty"><p><?= te('dev.no_games') ?></p><a class="btn primary" href="<?= e(app_url('/dev/games/new')) ?>"><?= te('dev.games.tao_du_an_dau_tien') ?></a></div>
        <?php else: ?>
            <div class="rows">
                <?php foreach (array_slice($games, 0, 8) as $g): ?>
                    <div class="row">
                        <div class="cell-game grow">
                            <?php if ($g['anh_bia']): ?><img src="<?= e(app_media_url($g['anh_bia'])) ?>" alt="" loading="lazy"><?php else: ?><span class="thumb"></span><?php endif; ?>
                            <div><a class="link" href="<?= e(app_url('/dev/games/' . (int)$g['ma'])) ?>"><?= e($g['tieu_de']) ?></a><span class="sub"><?= fmt_int((int)$g['so_huu']) ?> người sở hữu · <?= fmt_int((int)$g['luot_xem_30']) ?> lượt xem 30 ngày</span></div>
                        </div>
                        <?= status_pill('game_status', $g['trang_thai']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <div class="span-4 stack">
        <section class="panel" aria-labelledby="traffic-h">
            <header><h2 id="traffic-h"><?= te('dev.overview.30_ngay_qua') ?></h2></header>
            <dl class="facts">
                <dt><?= te('dev.overview.luot_xem_trang') ?></dt><dd><?= fmt_int($traffic['views']) ?></dd>
                <dt><?= te('dev.overview.luot_tai_xuong') ?></dt><dd><?= fmt_int($traffic['downloads']) ?></dd>
                <dt><?= te('dev.overview.luot_mua') ?></dt><dd><?= fmt_int($traffic['purchases']) ?></dd>
                <dt><?= te('dev.overview.doanh_thu_gop') ?></dt><dd><?= e(money($traffic['revenue'])) ?></dd>
            </dl>
        </section>
        <section class="panel" aria-labelledby="recent-h">
            <header><h2 id="recent-h"><?= te('dev.overview.giao_dich_gan_day') ?></h2><span class="meta"><a class="link" href="<?= e(app_url('/dev/sales')) ?>"><?= te('common.see_all') ?></a></span></header>
            <?php if (!$recent): ?><p class="muted"><?= te('dev.overview.chua_co_giao_dich_nao') ?></p><?php endif; ?>
            <div class="rows">
                <?php foreach ($recent as $r): ?>
                    <div class="row"><div class="grow"><?= e($r['tieu_de_luc_mua']) ?><span class="sub"><?= e(fmt_date($r['ngay_dat_hang'])) ?> · <?= e(label('ledger_status', $r['trang_thai'])) ?></span></div><span><?= e(money($r['so_tien_thuc_nhan'])) ?></span></div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>
