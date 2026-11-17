<?php
/** Create or edit a bundle. @var string $scope @var ?array $bundle @var array $games @var int[] $picked @var string[] $dropped @var int $sales */
$isAdmin = $scope === 'admin';
$base = $isAdmin ? '/admin/bundles' : '/dev/bundles';
$isNew = $bundle === null;
$id = (int)($bundle['ma'] ?? 0);
$hasOld = old('tieu_de', null) !== null;
$v = static fn(string $key) => old($key, (string)($bundle[$key] ?? ''));
$dt = static fn(string $key) => old($key, datetime_local($bundle[$key] ?? null));
$price = $hasOld ? old('gia') : ($bundle ? number_format((float)$bundle['gia'], 2, '.', '') : '');
$status = old('trang_thai', $bundle['trang_thai'] ?? 'draft');
$checked = array_map('intval', $hasOld ? (array)old('games', []) : $picked);
$statuses = [
    'draft'     => t('bundle.form.trang.thai.draft.mo.ta'),
    'published' => t('bundle.form.trang.thai.published.mo.ta'),
    'hidden'    => t('bundle.form.trang.thai.hidden.mo.ta'),
    'ended'     => t('bundle.form.trang.thai.ended.mo.ta'),
];
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url($base)) ?>"><?= $isAdmin ? te('nav.bundles') : te('nav.my_bundles') ?></a><span><?= $isNew ? te('dev.game-form.tao_moi') : e($bundle['tieu_de']) ?></span></nav>

<div class="title-row">
    <div>
        <h1><?= $isNew ? te('bundle.new') : e($bundle['tieu_de']) ?></h1>
        <?php if (!$isNew): ?><p class="sub"><?= te('bundle.form.da-ban-lan-cap-nhat', [ status_pill('bundle_status', $bundle['trang_thai']) ,  e(label('bundle_type', $bundle['loai_goi_game'])) ,  fmt_int($sales) ,  e(fmt_datetime($bundle['ngay_cap_nhat'])) ]) ?></p><?php endif; ?>
    </div>
    <?php if (!$isNew): ?><a class="btn" href="<?= e(app_url('/bundles/' . rawurlencode($bundle['duong_dan_rut_gon']))) ?>"><?= te('bundle.form.xem_trang_goi') ?></a><?php endif; ?>
</div>

<form class="form" method="post" action="<?= e(app_url($isNew ? $base : $base . '/' . $id)) ?>">
    <?= csrf_field() ?>
    <section class="settings-block" aria-labelledby="info-h">
        <div class="about">
            <h2 id="info-h"><?= te('game.details') ?></h2>
            <p><?= te('bundle.form.gia-goi-tinh-bang-nguoi-mua-da-co-mot-so-tro-choi-trong-goi-van-tra-du-gia-goi-trang-goi-cho-ho-biet-truoc-dieu-nay', [ e(APP_CURRENCY) ]) ?></p>
        </div>
        <div class="panel form-grid">
            <div class="field full">
                <label for="bd-title"><?= te('bundle.form.ten_goi') ?></label>
                <input class="input" id="bd-title" name="tieu_de" required maxlength="200" value="<?= e($v('tieu_de')) ?>">
            </div>
            <div class="field">
                <label for="bd-slug"><?= te('dev.game_slug') ?></label>
                <input class="input" id="bd-slug" name="duong_dan_rut_gon" maxlength="220" autocomplete="off" spellcheck="false" value="<?= e($v('duong_dan_rut_gon')) ?>">
                <span class="hint"><?= te('bundle.form.bo-trong-de-tao-tu-ten', [ $isNew ? '' : t('bundle.form.doi.gia.tri.nay.lam.hong.lien.ket.cu') ]) ?></span>
            </div>
            <div class="field">
                <label for="bd-price"><?= te('bundle.form.gia-goi', [ e(APP_CURRENCY) ]) ?></label>
                <input class="input" id="bd-price" name="gia" required inputmode="decimal" pattern="\d{1,9}([.,]\d{1,2})?" value="<?= e($price) ?>">
                <span class="hint"><?= te('bundle.form.chi_dat_khi_moi_tro') ?></span>
            </div>
            <div class="field">
                <label for="bd-start"><?= te('bundle.form.bat_dau_ban') ?></label>
                <input class="input" id="bd-start" type="datetime-local" name="ngay_bat_dau" value="<?= e($dt('ngay_bat_dau')) ?>">
            </div>
            <div class="field">
                <label for="bd-end"><?= te('bundle.form.ket_thuc_ban') ?></label>
                <input class="input" id="bd-end" type="datetime-local" name="ngay_ket_thuc" value="<?= e($dt('ngay_ket_thuc')) ?>">
                <span class="hint"><?= te('bundle.form.bo_trong_ca_hai_ban') ?></span>
            </div>
            <div class="field full">
                <label for="bd-desc"><?= te('game.about') ?></label>
                <textarea class="input" id="bd-desc" name="mo_ta" maxlength="20000" rows="5"><?= e($v('mo_ta')) ?></textarea>
            </div>
            <fieldset class="field full">
                <legend><?= te('common.status') ?></legend>
                <div class="choice-grid">
                    <?php foreach ($statuses as $k => $hint): ?>
                        <label class="check"><input type="radio" name="trang_thai" value="<?= e($k) ?>"<?= $status === $k ? ' checked' : '' ?>> <span><?= e(label('bundle_status', $k)) ?><span class="sub"><?= e($hint) ?></span></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        </div>
    </section>

    <section class="settings-block" aria-labelledby="games-h">
        <div class="about">
            <h2 id="games-h"><?= te('admin.games') ?></h2>
            <p><?= te('bundle.form.can-it-nhat-2-doanh-thu-goi-chia-cho-tung-tro-choi-theo-gia-le-roi-cho-cac-tac-gia-theo-ty-le-da-dat', [ $isAdmin ? te('bundle.form.moi_tro_choi_da_cong') : te('bundle.form.tro_choi_da_cong_bo') ]) ?></p>
            <?php if ($sales): ?><p><?= te('bundle.form.doi_tro_choi_khong_anh') ?></p><?php endif; ?>
        </div>
        <div class="panel">
            <?php if ($dropped): ?>
                <p class="hint"><?= te('bundle.form.se-bi-go-khoi-goi-khi-luu-vi-khong-con-chon-duoc', [ e(implode(', ', $dropped)) ]) ?></p>
            <?php endif; ?>
            <?php if (!$games): ?>
                <p class="muted"><?= te('bundle.form.chua_co_tro_choi_da') ?></p>
            <?php else: ?>
            <fieldset class="field">
                <legend class="sr-only"><?= te('bundle.games') ?></legend>
                <div class="check-list">
                    <?php foreach ($games as $g): $gid = (int)$g['ma']; $base_price = GameModel::basePrice($g); ?>
                        <label class="check"><input type="checkbox" name="games[]" value="<?= $gid ?>"<?= in_array($gid, $checked, true) ? ' checked' : '' ?>>
                            <span><?= e($g['tieu_de']) ?>
                                <span class="sub"><?= $base_price > 0 ? te('bundle.show.gia_le') . ' ' . e(money($base_price)) : te('price.free') ?><?= $isAdmin ? ' ' . te('common.dot') . ' ' . e($g['nguoi_tao']) : '' ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endif; ?>
        </div>
    </section>

    <div class="form-actions"><button class="btn primary" type="submit"><?= $isNew ? te('bundle.manage.tao_goi') : te('collection.show.luu_thay_doi') ?></button></div>
</form>
