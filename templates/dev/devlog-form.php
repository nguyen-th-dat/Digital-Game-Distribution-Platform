<?php
/** Write or edit a devlog post. @var array $game @var string $tab @var ?array $post */
$gid = (int)$game['ma'];
$isNew = $post === null;
$v = static fn(string $key, $fallback = '') => old($key, $post[$key] ?? $fallback);
$action = '/dev/games/' . $gid . '/devlogs' . ($isNew ? '' : '/' . (int)$post['ma']);
$hidden = !$isNew && $post['trang_thai'] === 'hidden';
$publicUrl = !$isNew && $post['trang_thai'] === 'published' && in_array($game['trang_thai'], ['published', 'unlisted'], true)
    ? app_url('/games/' . rawurlencode($game['duong_dan_rut_gon']) . '/devlogs/' . rawurlencode($post['duong_dan_rut_gon'])) : null;
?>
<?php partial('dev-game-tabs', ['game' => $game, 'tab' => $tab]); ?>

<div class="title-row">
    <div>
        <h2><?= $isNew ? te('dev.devlog-form.viet_bai_nhat_ky') : te('dev.devlog-form.sua_bai_nhat_ky') ?></h2>
        <?php if (!$isNew): ?><p class="sub"><?= status_pill('content_status', $post['trang_thai']) ?><?= $post['ngay_cong_bo'] ? ' ' . te('dev.devlog-form.dang') . ' ' . e(fmt_datetime($post['ngay_cong_bo'])) : '' ?> · đường dẫn /<?= e($post['duong_dan_rut_gon']) ?></p><?php endif; ?>
    </div>
    <div class="cluster">
        <a class="btn ghost" href="<?= e(app_url('/dev/games/' . $gid . '/devlogs')) ?>"><?= te('dev.devlog-form.tat_ca_bai_viet') ?></a>
        <?php if ($publicUrl): ?><a class="btn" href="<?= e($publicUrl) ?>"><?= te('dev.devlog-form.xem_bai') ?></a><?php endif; ?>
    </div>
</div>

<section class="panel narrow-panel">
    <form class="form" method="post" action="<?= e(app_url($action)) ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label for="d-title"><?= te('common.title') ?></label>
            <input class="input" id="d-title" name="tieu_de" maxlength="200" required value="<?= e($v('tieu_de')) ?>">
            <?php if ($isNew): ?><span class="hint"><?= te('dev.devlog-form.duong_dan_bai_viet_duoc') ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="d-body"><?= te('admin.comments') ?></label>
            <textarea class="input tall" id="d-body" name="noi_dung" maxlength="50000" required><?= e((string)$v('noi_dung')) ?></textarea>
            <span class="hint"><?= te('dev.devlog-form.van_ban_thuan_xuong_dong') ?></span>
        </div>
        <?php if ($hidden): ?>
            <p class="hint"><?= te('dev.devlog-form.bai_viet_da_bi_doi') ?></p>
        <?php else: ?>
            <fieldset class="field">
                <legend><?= te('common.status') ?></legend>
                <div class="choice-grid">
                    <label class="check"><input type="radio" name="trang_thai" value="draft"<?= $v('trang_thai', 'draft') === 'draft' ? ' checked' : '' ?>> <span><?= te('label.bundle_status.draft') ?><span class="sub"><?= te('dev.devlog-form.chi_tac_gia_thay') ?></span></span></label>
                    <label class="check"><input type="radio" name="trang_thai" value="published"<?= $v('trang_thai', 'draft') === 'published' ? ' checked' : '' ?>> <span><?= te('dev.devlog-form.dang') ?><span class="sub"><?= te('dev.devlog-form.hien_tren_trang_tro_choi') ?></span></span></label>
                </div>
            </fieldset>
        <?php endif; ?>
        <div class="form-actions"><button class="btn primary" type="submit"><?= $isNew ? te('dev.devlog-form.luu_bai_viet') : te('collection.show.luu_thay_doi') ?></button></div>
    </form>
</section>
