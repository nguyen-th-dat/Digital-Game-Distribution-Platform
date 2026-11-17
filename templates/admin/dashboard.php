<?php
/** Staff overview. @var bool $isAdmin @var array $kpis @var array $reviewQueue @var array $reports @var array $actions @var array $newUsers */
?>
<div class="title-row">
    <div><h1><?= te('admin.dashboard.tong_quan_quan_tri') ?></h1><p class="sub"><?= te('admin.dashboard.hang_doi_can_xu_ly') ?></p></div>
</div>

<section class="kpis" aria-label="<?= te('admin.dashboard.chi_so_chinh') ?>">
    <div class="kpi"><div class="kpi-label"><?= te('admin.dashboard.tai_khoan_hoat_dong') ?></div><div class="kpi-value"><?= fmt_int($kpis['users']) ?></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('admin.dashboard.tro_choi_da_cong_bo') ?></div><div class="kpi-value"><?= fmt_int($kpis['games']) ?></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('label.download_link_status.pending') ?></div><div class="kpi-value"><a class="link" href="<?= e(app_url('/admin/games?status=under_review')) ?>"><?= fmt_int($kpis['review']) ?></a></div></div>
    <div class="kpi"><div class="kpi-label"><?= te('admin.dashboard.bao_cao_chua_xu_ly') ?></div><div class="kpi-value"><a class="link" href="<?= e(app_url('/admin/reports')) ?>"><?= fmt_int($kpis['reports']) ?></a></div></div>
    <?php if ($isAdmin): ?>
        <div class="kpi"><div class="kpi-label"><?= te('order.request_refund') ?></div><div class="kpi-value"><a class="link" href="<?= e(app_url('/admin/refunds')) ?>"><?= fmt_int($kpis['refunds']) ?></a></div></div>
        <div class="kpi"><div class="kpi-label"><?= te('dev.request_payout') ?></div><div class="kpi-value"><a class="link" href="<?= e(app_url('/admin/payouts')) ?>"><?= fmt_int($kpis['payouts']) ?></a></div></div>
    <?php endif; ?>
</section>

<div class="grid top section">
    <section class="panel span-6" aria-labelledby="queue-h">
        <header><h2 id="queue-h"><?= te('admin.dashboard.tro_choi_cho_duyet') ?></h2><a class="link meta" href="<?= e(app_url('/admin/games?status=under_review')) ?>"><?= te('common.see_all') ?></a></header>
        <?php if (!$reviewQueue): ?><p class="muted"><?= te('admin.dashboard.khong_co_tro_choi_nao') ?></p><?php endif; ?>
        <div class="rows">
            <?php foreach ($reviewQueue as $g): ?>
                <div class="row">
                    <div class="grow"><a class="link" href="<?= e(app_url('/admin/games?q=' . rawurlencode($g['duong_dan_rut_gon']))) ?>"><?= e($g['tieu_de']) ?></a><span class="sub"><?= te('admin.dashboard.cap-nhat', [ e($g['ten_hien_thi']) ,  e(fmt_datetime($g['ngay_cap_nhat'])) ]) ?></span></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel span-6" aria-labelledby="reports-h">
        <header><h2 id="reports-h"><?= te('admin.dashboard.bao_cao_chua_xu_ly') ?></h2><a class="link meta" href="<?= e(app_url('/admin/reports')) ?>"><?= te('admin.dashboard.mo_hang_doi') ?></a></header>
        <?php if (!$reports): ?><p class="muted"><?= te('admin.dashboard.khong_co_bao_cao_nao') ?></p><?php endif; ?>
        <div class="rows">
            <?php foreach ($reports as $r): ?>
                <div class="row">
                    <div class="grow"><a class="link" href="<?= e(app_url('/admin/reports/' . (int)$r['ma'])) ?>"><?= e(label('report_target', $r['loai_doi_tuong'])) ?> #<?= (int)$r['doi_tuong_id'] ?> · <?= e(label('report_reason', $r['ma_ly_do'])) ?></a><span class="sub"><?= e($r['ten_hien_thi'] ?? te('admin.dashboard.tai_khoan_da_xoa')) ?> · <?= e(fmt_datetime($r['ngay_tao'])) ?></span></div>
                    <?= status_pill('report_status', $r['trang_thai']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel <?= $isAdmin ? 'span-7' : 'span-12' ?>" aria-labelledby="actions-h">
        <header><h2 id="actions-h"><?= te('admin.dashboard.thao_tac_kiem_duyet_gan') ?></h2><a class="link meta" href="<?= e(app_url('/admin/moderation')) ?>"><?= te('admin.dashboard.toan_bo_nhat_ky') ?></a></header>
        <?php if (!$actions): ?><p class="muted"><?= te('admin.dashboard.chua_co_thao_tac_nao') ?></p><?php endif; ?>
        <div class="rows">
            <?php foreach ($actions as $a): ?>
                <div class="row">
                    <div class="grow"><?= e(label('mod_action', $a['loai_hanh_dong'])) ?> · <a class="link" href="<?= e(app_url(mod_target_url($a['loai_doi_tuong'], (int)$a['doi_tuong_id']))) ?>"><?= e(label('report_target', $a['loai_doi_tuong'])) ?> #<?= (int)$a['doi_tuong_id'] ?></a><span class="sub"><?= e($a['ten_hien_thi']) ?> · <?= e(fmt_datetime($a['ngay_tao'])) ?><?= (string)$a['ly_do'] !== '' ? ' · ' . e(excerpt((string)$a['ly_do'], 90)) : '' ?></span></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if ($isAdmin): ?>
    <section class="panel span-5" aria-labelledby="new-users-h">
        <header><h2 id="new-users-h"><?= te('admin.dashboard.tai_khoan_moi') ?></h2><a class="link meta" href="<?= e(app_url('/admin/users')) ?>"><?= te('admin.dashboard.quan_ly_nguoi_dung') ?></a></header>
        <div class="rows">
            <?php foreach ($newUsers as $u): ?>
                <div class="row">
                    <img class="avatar" src="<?= e(avatar_url($u)) ?>" alt="" loading="lazy">
                    <div class="grow"><a class="link" href="<?= e(app_url('/admin/users/' . (int)$u['ma'])) ?>"><?= e($u['ten_hien_thi']) ?></a><span class="sub">@<?= e($u['ten_dang_nhap']) ?> · <?= e(fmt_date($u['ngay_tao'])) ?></span></div>
                    <?= status_pill('user_status', $u['trang_thai']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>
