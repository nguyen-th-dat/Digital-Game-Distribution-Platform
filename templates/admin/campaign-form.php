<?php
/** Create or edit a discount campaign. @var ?array $campaign @var array $games @var int[] $picked */
$isNew = $campaign === null;
$id = (int)($campaign['ma'] ?? 0);
$action = $isNew ? '/admin/campaigns' : '/admin/campaigns/' . $id;
$hasOld = old('tieu_de', null) !== null;
$v = static fn(string $key, $default = '') => old($key, $campaign[$key] ?? $default);
$dt = static fn(string $key) => old($key, datetime_local($campaign[$key] ?? null));
$pctValue = $hasOld ? old('phan_tram_giam_gia') : ($campaign ? rtrim(rtrim((string)$campaign['phan_tram_giam_gia'], '0'), '.') : '');
$storedMode = match ($campaign['trang_thai'] ?? 'draft') { 'draft' => 'draft', 'cancelled' => 'cancelled', default => 'run' };
$mode = old('che_do', $storedMode);
$checked = array_map('intval', $hasOld ? (array)old('tro_choi', []) : $picked);
$modes = [
    'draft'     => [t('admin.campaign-form.che_do.draft'), t('admin.campaign-form.che_do.draft.mo.ta')],
    'run'       => [t('admin.campaign-form.che_do.run'), t('admin.campaign-form.che_do.run.mo.ta')],
    'cancelled' => [t('admin.campaign-form.che_do.cancelled'), t('admin.campaign-form.che_do.cancelled.mo.ta')],
];
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url('/admin/campaigns')) ?>"><?= te('admin.campaigns') ?></a><span><?= $isNew ? te('dev.game-form.tao_moi') : e($campaign['tieu_de']) ?></span></nav>

<div class="title-row">
    <div>
        <h1><?= $isNew ? te('admin.campaigns.tao_chien_dich') : e($campaign['tieu_de']) ?></h1>
        <?php if (!$isNew): ?><p class="sub"><?= te('admin.campaign-form.tao', [ status_pill('campaign_status', DiscountCampaignModel::phase($campaign)) ,  e(fmt_datetime($campaign['ngay_tao'])) ]) ?></p><?php endif; ?>
    </div>
</div>

<form class="form" method="post" action="<?= e(app_url($action)) ?>">
    <?= csrf_field() ?>
    <section class="settings-block" aria-labelledby="info-h">
        <div class="about">
            <h2 id="info-h"><?= te('game.details') ?></h2>
            <p><?= te('admin.campaign-form.muc_giam_ap_dung_cho') ?></p>
        </div>
        <div class="panel form-grid">
            <div class="field full">
                <label for="cm-title"><?= te('common.title') ?></label>
                <input class="input" id="cm-title" name="tieu_de" minlength="3" maxlength="200" required value="<?= e($v('tieu_de')) ?>">
            </div>
            <div class="field">
                <label for="cm-pct"><?= te('admin.campaign-form.muc_giam') ?></label>
                <input class="input" id="cm-pct" type="number" name="phan_tram_giam_gia" min="1" max="95" step="0.01" required value="<?= e($pctValue) ?>">
                <span class="hint"><?= te('admin.campaign-form.tu_1_den_95') ?></span>
            </div>
            <div class="field new-row">
                <label for="cm-start"><?= te('admin.campaign-form.bat_dau') ?></label>
                <input class="input" id="cm-start" type="datetime-local" name="ngay_bat_dau" required value="<?= e($dt('ngay_bat_dau')) ?>">
            </div>
            <div class="field">
                <label for="cm-end"><?= te('admin.campaign-form.ket_thuc') ?></label>
                <input class="input" id="cm-end" type="datetime-local" name="ngay_ket_thuc" required value="<?= e($dt('ngay_ket_thuc')) ?>">
            </div>
            <div class="field full">
                <label for="cm-desc"><?= te('admin.campaign-form.mo_ta_noi_bo') ?></label>
                <textarea class="input" id="cm-desc" name="mo_ta" maxlength="5000" rows="3"><?= e((string)$v('mo_ta')) ?></textarea>
                <span class="hint"><?= te('admin.campaign-form.khong_hien_thi_cho_nguoi') ?></span>
            </div>
            <fieldset class="field full">
                <legend><?= te('common.status') ?></legend>
                <div class="choice-grid">
                    <?php foreach ($modes as $k => [$name, $hint]): ?>
                        <label class="check"><input type="radio" name="che_do" value="<?= e($k) ?>"<?= $mode === $k ? ' checked' : '' ?>> <span><?= e($name) ?><span class="sub"><?= e($hint) ?></span></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        </div>
    </section>

    <section class="settings-block" aria-labelledby="games-h">
        <div class="about">
            <h2 id="games-h"><?= te('admin.games') ?></h2>
            <p><?= te('admin.campaign-form.chi_liet_ke_tro_choi') ?></p>
        </div>
        <div class="panel">
            <?php if (!$games): ?>
                <p class="muted"><?= te('admin.campaign-form.chua_co_tro_choi_tra') ?></p>
            <?php else: ?>
            <fieldset class="field">
                <legend class="sr-only"><?= te('admin.campaign-form.tro_choi_duoc_giam_gia') ?></legend>
                <div class="check-list">
                    <?php foreach ($games as $g): $gid = (int)$g['ma']; ?>
                        <label class="check"><input type="checkbox" name="tro_choi[]" value="<?= $gid ?>"<?= in_array($gid, $checked, true) ? ' checked' : '' ?>>
                            <span><?= e($g['tieu_de']) ?>
                                <span class="sub"><?= e(money((float)$g['gia_toi_thieu'])) ?> · <?= e(label('game_status', $g['trang_thai'])) ?><?= $g['chien_dich_khac'] ? ' ' . te('admin.campaign-form.dot_cung_trong') . ' ' . e($g['chien_dich_khac']) : '' ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endif; ?>
        </div>
    </section>

    <div class="form-actions"><button class="btn primary" type="submit"><?= $isNew ? te('admin.campaigns.tao_chien_dich') : te('collection.show.luu_thay_doi') ?></button></div>
</form>
