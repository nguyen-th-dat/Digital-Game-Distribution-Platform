<?php
/** Staff game list. @var array $filters @var array $games @var array $counts @var array $staffStatuses @var int $total @var int $page @var int $pages */
$all = array_sum(array_map('intval', $counts));
?>
<div class="title-row">
    <div><h1><?= te('admin.games') ?></h1><p class="sub"><?= te('admin.games.duyet_tro_choi_gui_cong') ?></p></div>
</div>

<nav class="chips chips-row" aria-label="<?= te('admin.games.loc_theo_trang_thai') ?>">
    <a class="chip"<?= $filters['status'] === '' ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/games' . qs(['status' => null, 'page' => null]))) ?>"><?= te('common.all') ?> <span class="muted"><?= fmt_int($all) ?></span></a>
    <?php foreach (labels('game_status') as $k => $t): ?>
        <a class="chip"<?= $filters['status'] === $k ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/games' . qs(['status' => $k, 'page' => null]))) ?>"><?= e($t) ?> <span class="muted"><?= fmt_int((int)($counts[$k] ?? 0)) ?></span></a>
    <?php endforeach; ?>
</nav>

<form class="toolbar" method="get" action="<?= e(app_url('/admin/games')) ?>">
    <?php if ($filters['status'] !== ''): ?><input type="hidden" name="status" value="<?= e($filters['status']) ?>"><?php endif; ?>
    <div class="search">
        <label class="sr-only" for="g-q"><?= te('admin.games.tim_theo_ten_duong_dan') ?></label>
        <input class="input" id="g-q" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="<?= te('admin.games.ten_duong_dan_nguoi_tao') ?>" maxlength="100">
    </div>
    <span class="spacer"></span>
    <span class="count"><?= fmt_int($total) ?> trò chơi</span>
</form>

<?php if ($games): ?>
<section class="panel flush">
    <table>
        <thead><tr><th scope="col"><?= te('admin.games') ?></th><th scope="col"><?= te('common.status') ?></th><th scope="col"><?= te('common.price') ?></th><th scope="col" class="num"><?= te('admin.games.bao_cao_mo') ?></th><th scope="col"><?= te('admin.games.cap_nhat') ?></th><th scope="col" aria-label="<?= te('common.actions') ?>"></th></tr></thead>
        <tbody>
        <?php foreach ($games as $g): $gid = (int)$g['ma']; $isDraft = $g['trang_thai'] === 'draft'; ?>
            <tr>
                <td><?php if (in_array($g['trang_thai'], ['published', 'unlisted'], true)): ?><a class="link" href="<?= e(game_url($g)) ?>"><?= e($g['tieu_de']) ?></a><?php else: ?><?= e($g['tieu_de']) ?><?php endif; ?><span class="sub">#<?= $gid ?> · <?= e($g['ten_hien_thi']) ?> (@<?= e($g['ten_dang_nhap']) ?>)</span></td>
                <td><?= status_pill('game_status', $g['trang_thai']) ?></td>
                <td><?= e(label('pricing', $g['mo_hinh_gia'])) ?><?= (float)$g['gia_toi_thieu'] > 0 ? '<span class="sub">' . te('admin.games.tu') . ' ' . e(money($g['gia_toi_thieu'])) . '</span>' : '' ?></td>
                <td class="num"><?php if ((int)$g['bao_cao_mo'] > 0): ?><a class="link" href="<?= e(app_url('/admin/reports?target=game')) ?>"><?= fmt_int((int)$g['bao_cao_mo']) ?></a><?php else: ?>0<?php endif; ?></td>
                <td><?= e(fmt_date($g['ngay_cap_nhat'])) ?></td>
                <td class="actions">
                    <?php if ($isDraft): ?>
                        <span class="muted small"><?= te('admin.games.nha_phat_trien_dang_soan') ?></span>
                    <?php else: ?>
                        <details class="edit">
                            <summary class="link-btn"><?= te('admin.content.doi_trang_thai') ?></summary>
                            <form class="form well" method="post" action="<?= e(app_url('/admin/games/' . $gid . '/status')) ?>">
                                <?= csrf_field() ?><?= return_field() ?>
                                <div class="field"><label for="gs-<?= $gid ?>"><?= te('admin.content.trang_thai_moi') ?></label>
                                    <select class="input sm" id="gs-<?= $gid ?>" name="trang_thai">
                                        <?php foreach ($staffStatuses as $s): if ($s === $g['trang_thai']) continue; ?><option value="<?= e($s) ?>"><?= e(label('game_status', $s)) ?></option><?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="field"><label for="gr-<?= $gid ?>"><?= te('admin.reason') ?></label><input class="input sm" id="gr-<?= $gid ?>" name="ly_do" maxlength="1000"><span class="hint"><?= te('admin.games.bat_buoc_khi_an_hoac') ?></span></div>
                                <button class="btn sm primary" type="submit"><?= te('cart.apply') ?></button>
                            </form>
                        </details>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
<?php else: ?>
    <div class="empty"><p><?= te('admin.games.khong_co_tro_choi_nao') ?></p><a class="btn" href="<?= e(app_url('/admin/games')) ?>"><?= te('game.clear_filters') ?></a></div>
<?php endif; ?>
