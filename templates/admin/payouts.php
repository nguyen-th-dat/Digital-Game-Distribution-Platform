<?php
/** Developer payouts. @var string $status @var array $counts @var array $payouts @var array $accounts @var array $ledger */
$mask = static fn(string $acc): string => mb_strlen($acc) > 4 ? te('admin.payouts.dot_bullet') . ' ' . mb_substr($acc, -4) : $acc;
$ledgerBy = array_column($ledger, null, 'trang_thai');
$net = static fn(string $s): float => (float)($ledgerBy[$s]['net'] ?? 0);
$openCount = (int)($counts['requested'] ?? 0) + (int)($counts['processing'] ?? 0);
?>
<div class="title-row">
    <div><h1><?= te('admin.payouts.chi_tra_cho_nha_phat') ?></h1><p class="sub">Doanh thu được giữ <?= (int)PAYOUT_HOLD_DAYS ?> ngày rồi mới có thể rút. Nền tảng thu phí <?= rtrim(rtrim(number_format(PLATFORM_FEE_RATE * 100, 1), '0'), '.') ?>%.</p></div>
</div>

<section class="kpis" aria-label="<?= te('admin.payouts.so_ke_toan') ?>">
    <div class="kpi"><div class="kpi-label"><?= te('label.ledger_status.pending') ?></div><div class="kpi-value"><?= e(money($net('pending'))) ?></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('label.ledger_status.available') ?></div><div class="kpi-value"><?= e(money($net('available'))) ?></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('label.ledger_status.held') ?></div><div class="kpi-value"><?= e(money($net('held'))) ?></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('label.ledger_status.paid') ?></div><div class="kpi-value"><?= e(money($net('paid'))) ?></div></div>
</section>

<nav class="chips chips-row section" aria-label="<?= te('admin.games.loc_theo_trang_thai') ?>">
    <a class="chip"<?= $status === '' ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/payouts')) ?>"><?= te('admin.payouts.can_xu_ly') ?> <span class="muted"><?= fmt_int($openCount) ?></span></a>
    <?php foreach (labels('payout_status') as $k => $t): ?>
        <a class="chip"<?= $status === $k ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/payouts?status=' . $k)) ?>"><?= e($t) ?> <span class="muted"><?= fmt_int((int)($counts[$k] ?? 0)) ?></span></a>
    <?php endforeach; ?>
</nav>

<section class="panel" aria-labelledby="po-h">
    <header><h2 id="po-h"><?= te('admin.payouts.dot_chi_tra') ?></h2></header>
    <?php if (!$payouts): ?><p class="muted"><?= $status === '' ? 'Không có yêu cầu chi trả nào cần xử lý.' : 'Không có đợt chi trả nào ở trạng thái này.' ?></p><?php endif; ?>
    <div class="rows">
        <?php foreach ($payouts as $p): $pid = (int)$p['ma']; ?>
            <div class="row">
                <div class="grow">
                    <div>#<?= $pid ?> · <strong><?= e(money($p['so_tien'])) ?></strong> <?= te('admin.payouts.text') ?> <a class="link" href="<?= e(app_url('/admin/users/' . (int)$p['nguoi_dung_id'])) ?>"><?= e($p['ten_hien_thi']) ?></a></div>
                    <span class="sub"><?= e(label('payout_provider', $p['nha_cung_cap'])) ?> · <?= e($p['ten_tai_khoan'] ?: '') ?> <?= e($mask((string)$p['ma_tai_khoan_ben_ngoai'])) ?> · <?= fmt_int((int)$p['so_dong']) ?> dòng sổ · yêu cầu <?= e(fmt_datetime($p['ngay_yeu_cau'])) ?><?= $p['ma_chi_tra_nha_cung_cap'] ? ' ' . te('common.dot_ma') . ' ' . e($p['ma_chi_tra_nha_cung_cap']) : '' ?><?= $p['ly_do_that_bai'] ? ' ' . te('common.dot') . ' ' . e($p['ly_do_that_bai']) : '' ?></span>
                </div>
                <div class="stack">
                    <?= status_pill('payout_status', $p['trang_thai']) ?>
                    <?php if (in_array($p['trang_thai'], ['requested', 'processing'], true)): ?>
                        <details class="edit">
                            <summary class="link-btn"><?= te('admin.games.cap_nhat') ?></summary>
                            <form class="form well" method="post" action="<?= e(app_url('/admin/payouts/' . $pid)) ?>">
                                <?= csrf_field() ?>
                                <div class="field"><label for="ps-<?= $pid ?>"><?= te('admin.content.trang_thai_moi') ?></label>
                                    <select class="input sm" id="ps-<?= $pid ?>" name="trang_thai">
                                        <?php foreach (['processing', 'paid', 'failed', 'cancelled'] as $s): if ($s === $p['trang_thai']) continue; ?><option value="<?= $s ?>"><?= e(label('payout_status', $s)) ?></option><?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="field"><label for="pr-<?= $pid ?>"><?= te('admin.payouts.ma_tham_chieu_chuyen_khoan') ?></label><input class="input sm" id="pr-<?= $pid ?>" name="ma_tham_chieu" maxlength="190"><span class="hint"><?= te('admin.payouts.bat_buoc_khi_danh_dau') ?></span></div>
                                <div class="field"><label for="pl-<?= $pid ?>"><?= te('admin.reason') ?></label><input class="input sm" id="pl-<?= $pid ?>" name="ly_do" maxlength="1000"><span class="hint"><?= te('admin.payouts.bat_buoc_khi_that_bai') ?></span></div>
                                <button class="btn sm primary" type="submit"><?= te('common.save') ?></button>
                            </form>
                        </details>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
</section>

<section class="panel section" id="accounts" aria-labelledby="acc-h">
    <header><h2 id="acc-h"><?= te('admin.payouts.tai_khoan_nhan_tien') ?></h2><span class="meta"><?= fmt_int(count($accounts)) ?> tài khoản</span></header>
    <?php if (!$accounts): ?><p class="muted"><?= te('admin.payouts.chua_co_nha_phat_trien') ?></p><?php endif; ?>
    <div class="rows">
        <?php foreach ($accounts as $a): $aid = (int)$a['ma']; ?>
            <div class="row">
                <div class="grow"><?= e(label('payout_provider', $a['nha_cung_cap'])) ?> · <?= e($a['ten_tai_khoan'] ?: te('admin.payouts.khong_ten')) ?> <?= e($mask((string)$a['ma_tai_khoan_ben_ngoai'])) ?><span class="sub"><a class="link" href="<?= e(app_url('/admin/users/' . (int)$a['nguoi_dung_id'])) ?>"><?= e($a['ten_hien_thi']) ?></a> · <?= e($a['ma_tien_te']) ?><?= $a['mac_dinh'] ? ' ' . te('common.dot_mac_dinh') : '' ?> · thêm <?= e(fmt_date($a['ngay_tao'])) ?></span></div>
                <?= status_pill('payout_account_status', $a['trang_thai']) ?>
                <?php if ($a['trang_thai'] !== 'active'): ?>
                    <form method="post" action="<?= e(app_url('/admin/payout-accounts/' . $aid)) ?>"><?= csrf_field() ?><input type="hidden" name="trang_thai" value="active"><button class="btn sm" type="submit"><?= te('admin.payouts.xac_minh') ?></button></form>
                <?php endif; ?>
                <?php if ($a['trang_thai'] !== 'disabled'): ?>
                    <form method="post" action="<?= e(app_url('/admin/payout-accounts/' . $aid)) ?>" data-confirm="Vô hiệu hóa tài khoản nhận tiền này?"><?= csrf_field() ?><input type="hidden" name="trang_thai" value="disabled"><button class="btn sm danger" type="submit"><?= te('label.payout_account_status.disabled') ?></button></form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
