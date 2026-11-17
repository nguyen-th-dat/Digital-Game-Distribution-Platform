<?php
/** Coupon list and create form, shared by developers and administrators. @var string $scope @var array $coupons @var int $gameCount */
$isAdmin = $scope === 'admin';
$base = $isAdmin ? '/admin/coupons' : '/dev/coupons';
$amount = static fn(array $c): string => $c['loai_giam_gia'] === 'percent'
    ? rtrim(rtrim(number_format((float)$c['gia_tri_giam_gia'], 2, ',', ''), '0'), ',') . '%'
    : money((float)$c['gia_tri_giam_gia']);
$type = old('loai_giam_gia', 'percent');
?>
<div class="title-row">
    <div>
        <h1><?= te('cart.coupon') ?></h1>
        <p class="sub"><?= $isAdmin
            ? te('coupons.index.ma_do_quan_tri_vien')
            : te('coupons.index.ma_cua_ban_chi_giam') ?></p>
    </div>
</div>

<div class="grid top">
    <section class="panel span-8" aria-labelledby="list-h">
        <header><h2 id="list-h"><?= $isAdmin ? te('coupons.index.tat_ca_ma') : te('coupons.index.ma_cua_ban') ?></h2><span class="meta"><?= te('coupons.index.ma', [ fmt_int($total) ]) ?></span></header>
        <?php if (!$coupons): ?>
            <div class="empty"><p><?= te('coupon.empty') ?></p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th scope="col"><?= te('coupon.code') ?></th><th scope="col"><?= te('admin.campaigns.giam') ?></th><th scope="col" class="num"><?= te('coupon.used') ?></th><th scope="col"><?= te('coupons.index.hieu_luc') ?></th>
                    <th scope="col"><?= te('common.status') ?></th><th scope="col" class="num"><?= te('coupons.index.tong_da_giam') ?></th><th scope="col"><span class="sr-only"><?= te('common.actions') ?></span></th>
                </tr></thead>
                <tbody>
                <?php foreach ($coupons as $c): $state = CouponModel::state($c); $on = (int)$c['dang_hoat_dong'] === 1; ?>
                    <tr>
                        <td><code><?= e($c['ma_uu_dai']) ?></code>
                            <?php if ($isAdmin): ?><span class="sub"><?= (int)$c['toan_cua_hang'] ? te('coupons.index.toan_cua_hang') : te('coupons.index.chi_tro_choi_cua') ?><?= $c['nguoi_tao_dn'] ? ' ' . te('common.dot_at') . e($c['nguoi_tao_dn']) : '' ?></span><?php endif; ?>
                        </td>
                        <td><?= e($amount($c)) ?></td>
                        <td class="num"><?= fmt_int((int)$c['so_luot_da_dung']) ?><?= $c['so_luot_dung_toi_da'] !== null ? '/' . fmt_int((int)$c['so_luot_dung_toi_da']) : '' ?></td>
                        <td class="when"><?php if (!$c['ngay_bat_dau'] && !$c['ngay_ket_thuc']): ?><span class="muted"><?= te('bundle.manage.khong_gioi_han') ?></span><?php else: ?>
                            <?= $c['ngay_bat_dau'] ? te('bundle.manage.tu') . ' ' . e(fmt_datetime($c['ngay_bat_dau'])) : te('coupons.index.tu_khi_tao') ?><span class="sub"><?= $c['ngay_ket_thuc'] ? te('bundle.manage.den') . ' ' . e(fmt_datetime($c['ngay_ket_thuc'])) : te('bundle.manage.khong_het_han') ?></span>
                        <?php endif; ?></td>
                        <td><?= status_pill('coupon_state', $state) ?></td>
                        <td class="num"><?= e(money((float)$c['tong_giam'])) ?></td>
                        <td>
                            <form method="post" action="<?= e(app_url($base . '/' . (int)$c['ma'] . '/toggle')) ?>"<?= $on ? ' ' . 'data-confirm="' . te('coupons.index.tat_ma_xac_nhan') . ' ' . e($c['ma_uu_dai']) . '? ' . te('coupons.index.nguoi_mua_se_khong_ap') . '"' : '' ?>>
                                <?= csrf_field() ?><?= return_field() ?>
                                <button class="link-btn<?= $on ? ' neg' : '' ?>" type="submit"><?= $on ? te('coupons.index.tat') : te('coupons.index.bat') ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
        <?php endif; ?>
    </section>

    <section class="panel span-4" id="new" aria-labelledby="new-h">
        <header><h2 id="new-h"><?= te('coupons.index.tao_ma') ?></h2></header>
        <?php if (!$isAdmin && $gameCount === 0): ?>
            <p class="hint"><?= te('coupons.index.ban_chua_la_tac_gia') ?></p>
        <?php endif; ?>
        <form class="form" method="post" action="<?= e(app_url($base)) ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="cp-code"><?= te('coupon.code') ?></label>
                <input class="input" id="cp-code" name="ma_uu_dai" required minlength="4" maxlength="40" pattern="[A-Za-z0-9_\-]{4,40}" autocomplete="off" spellcheck="false" value="<?= e(old('ma_uu_dai')) ?>">
                <span class="hint"><?= te('coupons.index.4_40_ky_tu_chu') ?></span>
            </div>
            <fieldset class="field">
                <legend><?= te('coupons.index.loai_giam_gia') ?></legend>
                <div class="choice-grid">
                    <label class="check"><input type="radio" name="loai_giam_gia" value="percent"<?= $type === 'percent' ? ' checked' : '' ?>> <span><?= te('coupon.percent') ?><span class="sub"><?= te('coupons.index.tu_01_den_100') ?></span></span></label>
                    <label class="check"><input type="radio" name="loai_giam_gia" value="fixed_amount"<?= $type === 'fixed_amount' ? ' checked' : '' ?>> <span><?= te('coupons.index.so_tien') ?><span class="sub"><?= te('coupons.index.tinh-bang-khong-vuot-qua-gia-tri-don', [ e(APP_CURRENCY) ]) ?></span></span></label>
                </div>
            </fieldset>
            <div class="field">
                <label for="cp-value"><?= te('coupons.index.muc_giam') ?></label>
                <input class="input" id="cp-value" name="gia_tri_giam_gia" required inputmode="decimal" pattern="\d{1,9}([.,]\d{1,2})?" value="<?= e(old('gia_tri_giam_gia')) ?>">
            </div>
            <div class="field">
                <label for="cp-max"><?= te('coupons.index.so_luot_dung_toi_da') ?></label>
                <input class="input" id="cp-max" type="number" name="so_luot_dung_toi_da" min="1" max="1000000" value="<?= e(old('so_luot_dung_toi_da')) ?>">
                <span class="hint"><?= te('coupons.index.bo_trong_khong_gioi_han') ?></span>
            </div>
            <div class="field">
                <label for="cp-start"><?= te('admin.campaign-form.bat_dau') ?></label>
                <input class="input" id="cp-start" type="datetime-local" name="ngay_bat_dau" value="<?= e(old('ngay_bat_dau')) ?>">
            </div>
            <div class="field">
                <label for="cp-end"><?= te('admin.campaign-form.ket_thuc') ?></label>
                <input class="input" id="cp-end" type="datetime-local" name="ngay_ket_thuc" value="<?= e(old('ngay_ket_thuc')) ?>">
                <span class="hint"><?= te('coupons.index.bo_trong_ca_hai_dung') ?></span>
            </div>
            <div class="form-actions"><button class="btn primary" type="submit"><?= te('coupons.index.tao_ma') ?></button></div>
        </form>
    </section>
</div>
