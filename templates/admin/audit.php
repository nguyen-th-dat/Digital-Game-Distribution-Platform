<?php
/** System log. @var array $filters @var array $actions @var array $rows @var int $total @var int $page @var int $pages */
?>
<div class="title-row">
    <div><h1><?= te('admin.audit') ?></h1><p class="sub"><?= te('admin.audit.moi_thao_tac_quan_trong') ?></p></div>
</div>

<form class="toolbar" method="get" action="<?= e(app_url('/admin/audit')) ?>">
    <div class="search">
        <label class="sr-only" for="a-user"><?= te('admin.audit.ten_dang_nhap_hoac_email') ?></label>
        <input class="input" id="a-user" type="search" name="user" value="<?= e($filters['user']) ?>" placeholder="<?= te('admin.audit.ten_dang_nhap_hoac_email_2') ?>" maxlength="100">
    </div>
    <label class="sr-only" for="a-action"><?= te('admin.audit.hanh_dong') ?></label>
    <select class="input" id="a-action" name="action" data-autosubmit>
        <option value=""><?= te('admin.audit.moi_hanh_dong') ?></option>
        <?php foreach ($actions as $a): ?><option value="<?= e($a) ?>"<?= $filters['action'] === $a ? ' selected' : '' ?>><?= e($a) ?></option><?php endforeach; ?>
    </select>
    <span class="spacer"></span>
    <span class="count"><?= te('admin.audit.ban-ghi', [ fmt_int($total) ]) ?></span>
</form>

<?php if ($rows): ?>
<section class="panel flush">
    <table>
        <thead><tr><th scope="col"><?= te('admin.audit.thoi_diem') ?></th><th scope="col"><?= te('admin.audit.nguoi_thuc_hien') ?></th><th scope="col"><?= te('admin.audit.hanh_dong') ?></th><th scope="col"><?= te('admin.audit.doi_tuong') ?></th><th scope="col"><?= te('common.details') ?></th><th scope="col">IP</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e(fmt_datetime($r['ngay_tao'])) ?></td>
                <td><?php if ($r['nguoi_dung_id']): ?><a class="link" href="<?= e(app_url('/admin/users/' . (int)$r['nguoi_dung_id'])) ?>"><?= e($r['ten_hien_thi'] ?? ('#' . (int)$r['nguoi_dung_id'])) ?></a><?php else: ?><span class="muted"><?= te('game.system') ?></span><?php endif; ?></td>
                <td><code><?= e($r['ma_hanh_dong']) ?></code></td>
                <td><?= $r['loai_thuc_the'] ? e($r['loai_thuc_the']) . ($r['thuc_the_id'] ? ' #' . (int)$r['thuc_the_id'] : '') : '—' ?></td>
                <td><span class="truncate"><?= $r['du_lieu_bo_sung'] ? e(excerpt((string)$r['du_lieu_bo_sung'], 120)) : te('common.emdash') ?></span></td>
                <td class="small muted"><?= e((string)$r['dia_chi_ip']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
<?php else: ?>
    <div class="empty"><p><?= te('admin.audit.khong_co_ban_ghi_nao') ?></p><a class="btn" href="<?= e(app_url('/admin/audit')) ?>"><?= te('game.clear_filters') ?></a></div>
<?php endif; ?>
