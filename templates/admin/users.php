<?php
/** Admin user list. @var array $filters @var array $users @var int $total @var int $page @var int $pages */
?>
<div class="title-row">
    <div><h1><?= te('admin.user') ?></h1><p class="sub"><?= te('admin.users.tim_tai_khoan_doi_vai') ?></p></div>
</div>

<form class="toolbar" method="get" action="<?= e(app_url('/admin/users')) ?>">
    <div class="search">
        <label class="sr-only" for="u-q"><?= te('admin.users.tim_theo_ten_dang_nhap') ?></label>
        <input class="input" id="u-q" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="<?= te('admin.users.ten_dang_nhap_email_ten') ?>" maxlength="100">
    </div>
    <label class="sr-only" for="u-status"><?= te('common.status') ?></label>
    <select class="input" id="u-status" name="status" data-autosubmit>
        <option value=""><?= te('admin.content.moi_trang_thai') ?></option>
        <?php foreach (labels('user_status') as $k => $t): ?><option value="<?= e($k) ?>"<?= $filters['status'] === $k ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
    </select>
    <label class="sr-only" for="u-role"><?= te('admin.roles') ?></label>
    <select class="input" id="u-role" name="role" data-autosubmit>
        <option value=""><?= te('admin.users.moi_vai_tro') ?></option>
        <?php foreach (labels('role') as $k => $t): ?><option value="<?= e($k) ?>"<?= $filters['role'] === $k ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
    </select>
    <span class="spacer"></span>
    <span class="count"><?= fmt_int($total) ?> tài khoản</span>
</form>

<?php if ($users): ?>
<section class="panel flush">
    <table>
        <thead><tr><th scope="col"><?= te('nav.account') ?></th><th scope="col"><?= te('admin.roles') ?></th><th scope="col"><?= te('common.status') ?></th><th scope="col" class="num"><?= te('admin.user.tro_choi_so_huu') ?></th><th scope="col"><?= te('common.created_at') ?></th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): $roles = array_filter(explode(',', (string)$u['vai_tro'])); ?>
            <tr>
                <td><a class="link" href="<?= e(app_url('/admin/users/' . (int)$u['ma'])) ?>"><?= e($u['ten_hien_thi']) ?></a><span class="sub">@<?= e($u['ten_dang_nhap']) ?> · <?= e($u['email']) ?></span></td>
                <td><?= $roles ? e(implode(', ', array_map(static fn($r) => label('role', $r), $roles))) : te('common.emdash') ?></td>
                <td><?= status_pill('user_status', $u['trang_thai']) ?></td>
                <td class="num"><?= fmt_int((int)$u['so_game']) ?></td>
                <td><?= e(fmt_date($u['ngay_tao'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
<?php else: ?>
    <div class="empty"><p><?= te('admin.users.khong_co_tai_khoan_nao') ?></p><a class="btn" href="<?= e(app_url('/admin/users')) ?>"><?= te('game.clear_filters') ?></a></div>
<?php endif; ?>
