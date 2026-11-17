<?php
/** Developer payouts and payout accounts. @var array $balances @var array $accounts @var array $payouts @var bool $openPayout @var int $maxAccounts */
$mask = static fn(string $acc): string => mb_strlen($acc) > 4 ? te('admin.payouts.dot_bullet') . ' ' . mb_substr($acc, -4) : $acc;
$hasActive = (bool)array_filter($accounts, static fn($a) => $a['trang_thai'] === 'active');
$canRequest = $hasActive && !$openPayout && $balances['available'] > 0;
?>
<div class="title-row">
    <div><h1><?= te('admin.payouts') ?></h1><p class="sub">Doanh thu chuyển sang "Có thể rút" sau <?= (int)PAYOUT_HOLD_DAYS ?> ngày. Mỗi yêu cầu gom toàn bộ số dư có thể rút vào một đợt chi trả.</p></div>
</div>

<div class="grid top">
    <div class="span-8 stack">
        <section class="panel" aria-labelledby="req-h">
            <header><h2 id="req-h"><?= te('dev.payouts.so_du_co_the_rut') ?></h2></header>
            <div class="row">
                <div class="grow"><span class="kpi-value"><?= e(money($balances['available'])) ?></span><span class="sub">Đang giữ <?= e(money($balances['pending'])) ?> · đang chi trả <?= e(money($balances['held'])) ?> · đã chi trả <?= e(money($balances['paid'])) ?></span></div>
                <?php if ($canRequest): ?>
                    <form method="post" action="<?= e(app_url('/dev/payouts/request')) ?>" data-confirm="Gửi yêu cầu rút <?= e(money($balances['available'])) ?>?"><?= csrf_field() ?><button class="btn primary" type="submit"><?= te('dev.request_payout') ?></button></form>
                <?php endif; ?>
            </div>
            <?php if (!$canRequest): ?>
                <p class="hint"><?= !$hasActive ? te('dev.payouts.can_it_nhat_mot_tai') : ($openPayout ? te('dev.payouts.ban_dang_co_mot_yeu') : te('dev.payouts.chua_co_khoan_nao_co')) ?></p>
            <?php endif; ?>
        </section>

        <section class="panel" aria-labelledby="hist-h">
            <header><h2 id="hist-h"><?= te('dev.payouts.lich_su_chi_tra') ?></h2></header>
            <?php if (!$payouts): ?><p class="muted"><?= te('dev.payouts.chua_co_dot_chi_tra') ?></p><?php endif; ?>
            <div class="rows">
                <?php foreach ($payouts as $p): ?>
                    <div class="row">
                        <div class="grow"><strong><?= e(money($p['so_tien'])) ?></strong> · <?= e(label('payout_provider', $p['nha_cung_cap'])) ?><span class="sub">#<?= (int)$p['ma'] ?> · yêu cầu <?= e(fmt_datetime($p['ngay_yeu_cau'])) ?> · <?= fmt_int((int)$p['so_dong']) ?> dòng sổ<?= $p['ngay_xu_ly'] ? ' ' . te('common.dot_xu_ly') . ' ' . e(fmt_datetime($p['ngay_xu_ly'])) : '' ?><?= $p['ma_chi_tra_nha_cung_cap'] ? ' ' . te('common.dot_ma') . ' ' . e($p['ma_chi_tra_nha_cung_cap']) : '' ?><?= $p['ly_do_that_bai'] ? ' ' . te('common.dot') . ' ' . e($p['ly_do_that_bai']) : '' ?></span></div>
                        <?= status_pill('payout_status', $p['trang_thai']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="span-4 stack">
        <section class="panel" aria-labelledby="acc-h">
            <header><h2 id="acc-h"><?= te('admin.payouts.tai_khoan_nhan_tien') ?></h2><span class="meta"><?= count($accounts) ?>/<?= (int)$maxAccounts ?></span></header>
            <?php if (!$accounts): ?><p class="muted"><?= te('dev.payouts.chua_co_phuong_thuc_nhan') ?></p><?php endif; ?>
            <div class="rows">
                <?php foreach ($accounts as $a): ?>
                    <div class="row">
                        <div class="grow"><?= e(label('payout_provider', $a['nha_cung_cap'])) ?><span class="sub"><?= e($a['ten_tai_khoan'] ?: '') ?> <?= e($mask((string)$a['ma_tai_khoan_ben_ngoai'])) ?><?= $a['mac_dinh'] ? ' ' . te('common.dot_mac_dinh') : '' ?></span></div>
                        <div class="stack">
                            <?= status_pill('payout_account_status', $a['trang_thai']) ?>
                            <?php if (!$a['mac_dinh'] && $a['trang_thai'] !== 'disabled'): ?>
                                <form method="post" action="<?= e(app_url('/dev/payouts/accounts/' . (int)$a['ma'] . '/default')) ?>"><?= csrf_field() ?><button class="link-btn" type="submit"><?= te('dev.payouts.dat_mac_dinh') ?></button></form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (count($accounts) < $maxAccounts): ?>
        <section class="panel" id="add-account" aria-labelledby="add-h">
            <header><h2 id="add-h"><?= te('dev.payouts.them_phuong_thuc') ?></h2></header>
            <form class="form" method="post" action="<?= e(app_url('/dev/payouts/accounts')) ?>">
                <?= csrf_field() ?>
                <div class="field"><label for="pa-p"><?= te('dev.payouts.phuong_thuc') ?></label>
                    <select class="input" id="pa-p" name="nha_cung_cap" required>
                        <option value=""><?= te('dev.game-media.chon') ?></option>
                        <?php foreach (labels('payout_provider') as $k => $t): ?><option value="<?= e($k) ?>"<?= old('nha_cung_cap') === $k ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="field"><label for="pa-n"><?= te('dev.payouts.ten_chu_tai_khoan') ?></label><input class="input" id="pa-n" name="ten_tai_khoan" maxlength="120" required value="<?= e(old('ten_tai_khoan')) ?>"></div>
                <div class="field"><label for="pa-a"><?= te('dev.payouts.so_tai_khoan_email_paypal') ?></label><input class="input" id="pa-a" name="ma_tai_khoan_ben_ngoai" maxlength="255" required autocomplete="off" value="<?= e(old('ma_tai_khoan_ben_ngoai')) ?>"><span class="hint"><?= te('dev.payouts.ngan_hang_6_34_chu') ?></span></div>
                <p class="hint">Tiền tệ nhận: <?= e(APP_CURRENCY) ?>. Quản trị viên xác minh trước khi phương thức được dùng để chi trả.</p>
                <div class="form-actions"><button class="btn primary" type="submit"><?= te('common.more') ?></button></div>
            </form>
        </section>
        <?php endif; ?>
    </div>
</div>
