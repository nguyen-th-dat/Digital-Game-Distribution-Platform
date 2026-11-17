<?php
/** User content moderation. @var array $filters @var array $types @var array $statusesFor @var array $items @var int $total @var int $page @var int $pages */
$filtered = $filters['type'] !== '' || $filters['status'] !== '' || $filters['q'] !== '';
?>
<div class="title-row">
    <div><h1><?= te('admin.content.noi_dung_nguoi_dung') ?></h1><p class="sub"><?= te('admin.content.danh_gia_binh_luan_nhat') ?></p></div>
</div>

<nav class="chips chips-row" aria-label="<?= te('admin.content.loc_theo_loai_noi_dung') ?>">
    <a class="chip"<?= $filters['type'] === '' ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/comments' . qs(['type' => null, 'id' => null, 'page' => null]))) ?>"><?= te('common.all') ?></a>
    <?php foreach ($types as $t): ?>
        <a class="chip"<?= $filters['type'] === $t ? ' aria-current="true"' : '' ?> href="<?= e(app_url('/admin/comments' . qs(['type' => $t, 'id' => null, 'page' => null]))) ?>"><?= e(label('report_target', $t)) ?></a>
    <?php endforeach; ?>
</nav>

<form class="toolbar" method="get" action="<?= e(app_url('/admin/comments')) ?>">
    <?php if ($filters['type'] !== ''): ?><input type="hidden" name="type" value="<?= e($filters['type']) ?>"><?php endif; ?>
    <div class="search">
        <label class="sr-only" for="c-q"><?= te('admin.content.tim_trong_noi_dung') ?></label>
        <input class="input" id="c-q" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="<?= te('admin.content.tim_trong_noi_dung_2') ?>" maxlength="100">
    </div>
    <label class="sr-only" for="c-status"><?= te('common.status') ?></label>
    <select class="input" id="c-status" name="status" data-autosubmit>
        <option value=""><?= te('admin.content.moi_trang_thai') ?></option>
        <?php foreach (['published', 'hidden', 'removed'] as $k): ?><option value="<?= e($k) ?>"<?= $filters['status'] === $k ? ' selected' : '' ?>><?= e(label('content_status', $k)) ?></option><?php endforeach; ?>
    </select>
    <span class="spacer"></span>
    <span class="count"><?= te('admin.content.muc', [ fmt_int($total) ,  $filters['id'] > 0 && $filters['type'] !== '' ? t('admin.content.ma', [(int)$filters['id']]) : '' ]) ?></span>
</form>

<?php if ($items): ?>
<section class="panel">
    <div class="rows">
        <?php foreach ($items as $c): $kind = $c['kind']; $cid = (int)$c['ma']; $key = $kind . '-' . $cid; ?>
            <div class="row" id="item-<?= e($key) ?>">
                <div class="grow">
                    <div class="small muted"><?= e(label('report_target', $kind)) ?> #<?= $cid ?> · <?= e($c['ngu_canh']) ?></div>
                    <?php if ((string)$c['tieu_de'] !== ''): ?><strong><?= e($c['tieu_de']) ?></strong><?php endif; ?>
                    <p><?= e(excerpt((string)$c['noi_dung'], 260)) ?></p>
                    <span class="sub"><?= e($c['ten_hien_thi']) ?> @<?= e($c['ten_dang_nhap']) ?> · <?= e(fmt_datetime($c['ngay_tao'])) ?><?php if ($c['trang_thai'] === 'published'): ?> · <a class="link" href="<?= e(app_url($c['url'])) ?>"><?= te('admin.content.xem_tai_trang_goc') ?></a><?php endif; ?></span>
                </div>
                <div class="stack">
                    <?= status_pill('content_status', $c['trang_thai']) ?>
                    <details class="edit">
                        <summary class="link-btn"><?= te('admin.content.doi_trang_thai') ?></summary>
                        <form class="form well" method="post" action="<?= e(app_url('/admin/content/' . rawurlencode($kind) . '/' . $cid)) ?>">
                            <?= csrf_field() ?><?= return_field() ?>
                            <div class="field"><label for="cs-<?= e($key) ?>"><?= te('admin.content.trang_thai_moi') ?></label>
                                <select class="input sm" id="cs-<?= e($key) ?>" name="trang_thai">
                                    <?php foreach ($statusesFor[$kind] as $s): if ($s === $c['trang_thai']) continue; ?><option value="<?= e($s) ?>"><?= e(label('content_status', $s)) ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="field"><label for="cr-<?= e($key) ?>"><?= te('admin.reason') ?></label><input class="input sm" id="cr-<?= e($key) ?>" name="ly_do" maxlength="1000"><span class="hint"><?= te('admin.content.bat_buoc_khi_an_hoac') ?></span></div>
                            <button class="btn sm primary" type="submit"><?= te('cart.apply') ?></button>
                        </form>
                    </details>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
<?php else: ?>
    <div class="empty"><p><?= $filtered ? te('admin.content.khong_co_noi_dung_nao') : te('admin.content.chua_co_noi_dung_nao') ?></p><?php if ($filtered): ?><a class="btn" href="<?= e(app_url('/admin/comments')) ?>"><?= te('game.clear_filters') ?></a><?php endif; ?></div>
<?php endif; ?>
