<?php
/** Refund queue. @var string $status @var array $counts @var array $refunds @var int $total @var int $page @var int $pages */
?>
<div class="title-row">
    <div><h1><?= te('admin.refunds') ?></h1><p class="sub"><?= te('admin.refunds.duyet-yeu-cau-hoan-tien-trong-ngay-sau-thanh-toan-duyet-se-hoan-tien-dao-doanh-thu-cua-nha-phat-trien-va-thu-hoi-quyen-so-huu', [ (int)REFUND_WINDOW_DAYS ]) ?></p></div>
</div>

<nav class="chips chips-row" aria-label="<?= te('admin.games.loc_theo_trang_thai') ?>">
    <?php foreach (labels('refund_status') as $k => $t): if ($k !== 'requested' && empty($counts[$k])) continue; ?>
        <a class="chip"<?= $status === $k ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/refunds' . qs(['status' => $k, 'page' => null]))) ?>"><?= e($t) ?> <span class="muted"><?= fmt_int((int)($counts[$k] ?? 0)) ?></span></a>
    <?php endforeach; ?>
    <a class="chip"<?= $status === '' ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/refunds' . qs(['status' => '', 'page' => null]))) ?>"><?= te('common.all') ?> <span class="muted"><?= fmt_int(array_sum(array_map('intval', $counts))) ?></span></a>
</nav>

<?php if ($refunds): ?>
<section class="panel">
    <div class="rows">
        <?php foreach ($refunds as $r): $rid = (int)$r['ma']; ?>
            <div class="row" id="refund-<?= $rid ?>">
                <div class="grow">
                    <div><?= e($r['tieu_de_luc_mua'] ?? te('admin.refunds.toan_bo_don')) ?> · <strong><?= e(money($r['so_tien'])) ?></strong></div>
                    <span class="sub"><?= te('admin.refunds.don', [ e($r['ma_don_hang']) ]) ?> <?php if ($r['nguoi_yeu_cau_id']): ?><a class="link" href="<?= e(app_url('/admin/users/' . (int)$r['nguoi_yeu_cau_id'])) ?>"><?= e($r['buyer_name']) ?></a><?php else: ?><?= te('admin.refunds.tai-khoan-da-xoa') ?><?php endif; ?> <?= te('admin.refunds.gui', [ e(fmt_datetime($r['ngay_tao'])) ,  $r['ngay_xu_ly'] ? ' ' . te('common.dot_xu_ly') . ' ' . e(fmt_datetime($r['ngay_xu_ly'])) : '' ]) ?></span>
                    <?php if ((string)$r['ly_do'] !== ''): ?><p class="well"><?= nl2br(e($r['ly_do'])) ?></p><?php endif; ?>
                </div>
                <div class="stack">
                    <?= status_pill('refund_status', $r['trang_thai']) ?>
                    <?php if ($r['trang_thai'] === 'requested'): ?>
                        <details class="edit">
                            <summary class="link-btn"><?= te('admin.refunds.xu_ly') ?></summary>
                            <form class="form well" method="post" action="<?= e(app_url('/admin/refunds/' . $rid)) ?>" data-confirm="<?= te('admin.refunds.xac-nhan-quyet-dinh-hoan-tien-e-money-r-so-tien') ?>">
                                <?= csrf_field() ?><?= return_field() ?>
                                <fieldset class="field">
                                    <legend><?= te('admin.refunds.quyet_dinh') ?></legend>
                                    <label class="check"><input type="radio" name="quyet_dinh" value="approve" required> <span><?= te('admin.refunds.duyet_va_hoan_tien') ?></span></label>
                                    <label class="check"><input type="radio" name="quyet_dinh" value="reject"> <span><?= te('label.refund_status.rejected') ?></span></label>
                                </fieldset>
                                <div class="field"><label for="rn-<?= $rid ?>"><?= te('admin.refunds.ghi_chu_cho_nguoi_mua') ?></label><textarea class="input" id="rn-<?= $rid ?>" name="ghi_chu" maxlength="1000"></textarea><span class="hint"><?= te('admin.refunds.bat_buoc_khi_tu_choi') ?></span></div>
                                <button class="btn sm primary" type="submit"><?= te('common.confirm') ?></button>
                            </form>
                        </details>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
<?php else: ?>
    <div class="empty"><p><?= $status === 'requested' ? te('admin.refunds.khong_co_yeu_cau_hoan') : te('admin.refunds.khong_co_yeu_cau_nao') ?></p></div>
<?php endif; ?>
