<?php
/**
 * Create or edit a project. @var ?array $game @var string $tab @var array $picked
 * @var array $categories @var array $tags @var array $platforms
 */
$isNew = $game === null;
$hasOld = old('tieu_de', null) !== null;
$v = static fn(string $key, $fallback = '') => old($key, $game[$key] ?? $fallback);
$flag = static fn(string $key) => $hasOld ? old($key, '') !== '' : (int)($game[$key] ?? 1) === 1;
$money = static fn($value) => $value === null || $value === '' ? '' : rtrim(rtrim(number_format((float)$value, 2, '.', ''), '0'), '.');
$statusLocked = !$isNew && !in_array($game['trang_thai'], DevGameController::DEV_STATUSES, true);
$action = $isNew ? '/dev/games' : '/dev/games/' . (int)$game['ma'];
$checks = static function (string $name, array $items, string $text, array $selected): void {
    foreach ($items as $item) {
        $id = (int)$item['ma'];
        echo '<label class="check"><input type="checkbox" name="' . e($name) . '[]" value="' . $id . '"' . (in_array($id, $selected, true) ? ' checked' : '') . '> <span>' . e($item[$text]) . '</span></label>';
    }
};
?>
<?php if ($isNew): ?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url('/dev/games')) ?>"><?= te('dev.games') ?></a><span><?= te('dev.game-form.tao_moi') ?></span></nav>
<div class="title-row"><div><h1><?= te('dev.create_game') ?></h1><p class="sub"><?= te('dev.game-form.du_an_moi_bat_dau') ?></p></div></div>
<?php else: ?>
<?php partial('dev-game-tabs', ['game' => $game, 'tab' => $tab]); ?>
<?php endif; ?>

<form method="post" action="<?= e(app_url($action)) ?>">
    <?= csrf_field() ?>
    <section class="settings-block" aria-labelledby="basic-h">
        <div class="about"><h2 id="basic-h"><?= te('dev.game-form.thong_tin_co_ban') ?></h2><p><?= te('dev.game-form.ten_duong_dan_va_mo') ?></p></div>
        <div class="panel form">
            <div class="field">
                <label for="g-title"><?= te('dev.game-form.ten_du_an') ?></label>
                <input class="input" id="g-title" name="tieu_de" maxlength="200" required value="<?= e($v('tieu_de')) ?>">
            </div>
            <div class="field">
                <label for="g-slug"><?= te('admin.campaign-form.duong_dan') ?></label>
                <input class="input" id="g-slug" name="duong_dan_rut_gon" maxlength="220" pattern="[a-z0-9]+(-[a-z0-9]+)*" value="<?= e($v('duong_dan_rut_gon')) ?>" placeholder="<?= te('admin.jam-form.tu_tao_tu_ten_neu') ?>">
                <span class="hint">Chữ thường không dấu, số và dấu gạch ngang.<?= !$isNew && $game['ngay_cong_bo'] ? ' ' . te('dev.game-form.doi_duong_dan_sau_khi') : '' ?></span>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label for="g-type"><?= te('dev.game-form.loai_du_an') ?></label>
                    <select class="input" id="g-type" name="loai_du_an" required>
                        <?php foreach (GameModel::PROJECT_TYPES as $t): ?><option value="<?= e($t) ?>"<?= $v('loai_du_an', 'game') === $t ? ' selected' : '' ?>><?= e(label('project_type', $t)) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="g-release"><?= te('dev.game-form.tinh_trang_phat_trien') ?></label>
                    <select class="input" id="g-release" name="trang_thai_phat_hanh" required>
                        <?php foreach (GameModel::RELEASE_STATUSES as $s): ?><option value="<?= e($s) ?>"<?= $v('trang_thai_phat_hanh', 'in_development') === $s ? ' selected' : '' ?>><?= e(label('release_status', $s)) ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field">
                <label for="g-short"><?= te('dev.game_summary') ?></label>
                <textarea class="input" id="g-short" name="mo_ta_ngan" maxlength="500" rows="3"><?= e((string)$v('mo_ta_ngan')) ?></textarea>
                <span class="hint"><?= te('dev.game-form.toi_da_500_ky_tu') ?></span>
            </div>
            <div class="field">
                <label for="g-desc"><?= te('dev.game-form.mo_ta_chi_tiet') ?></label>
                <textarea class="input tall" id="g-desc" name="mo_ta" maxlength="20000"><?= e((string)$v('mo_ta')) ?></textarea>
                <span class="hint"><?= te('dev.game-form.van_ban_thuan_xuong_dong') ?></span>
            </div>
        </div>
    </section>

    <section class="settings-block" aria-labelledby="price-h">
        <div class="about"><h2 id="price-h"><?= te('common.price') ?></h2><p>Giá tính bằng <?= e(APP_CURRENCY) ?>. Nền tảng thu phí <?= rtrim(rtrim(number_format(PLATFORM_FEE_RATE * 100, 1), '0'), '.') ?>% mỗi lượt bán; phần còn lại chia cho các tác giả theo tỷ lệ.</p></div>
        <div class="panel form">
            <fieldset class="field">
                <legend><?= te('dev.price_model') ?></legend>
                <div class="choice-grid">
                    <?php foreach (GameModel::PRICING as $p): ?>
                        <label class="check"><input type="radio" name="mo_hinh_gia" value="<?= e($p) ?>" required<?= $v('mo_hinh_gia', 'free') === $p ? ' checked' : '' ?>> <span><?= e(label('pricing', $p)) ?></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <div class="form-grid">
                <div class="field">
                    <label for="g-min"><?= te('dev.game-form.gia_gia_toi_thieu') ?></label>
                    <input class="input" id="g-min" name="gia_toi_thieu" inputmode="decimal" pattern="\d{1,9}([.,]\d{1,2})?" value="<?= e($money($v('gia_toi_thieu'))) ?>" placeholder="0">
                    <span class="hint"><?= te('dev.game-form.tra_phi_gia_ban_lon') ?></span>
                </div>
                <div class="field">
                    <label for="g-sug"><?= te('dev.game-form.gia_de_xuat') ?></label>
                    <input class="input" id="g-sug" name="gia_de_xuat" inputmode="decimal" pattern="\d{1,9}([.,]\d{1,2})?" value="<?= e($money($v('gia_de_xuat'))) ?>">
                    <span class="hint"><?= te('dev.game-form.chi_dung_cho_tra_tuy') ?></span>
                </div>
            </div>
        </div>
    </section>

    <section class="settings-block" aria-labelledby="tax-h">
        <div class="about"><h2 id="tax-h"><?= te('dev.game-form.phan_loai') ?></h2><p><?= te('dev.game-form.giup_nguoi_choi_tim_thay') ?></p></div>
        <div class="panel form">
            <fieldset class="field">
                <legend><?= te('common.categories') ?> <span class="muted"><?= te('dev.game-form.toi_da_3') ?></span></legend>
                <div class="choice-grid"><?php $checks('the_loai', $categories, 'ten', $picked['the_loai']); ?></div>
            </fieldset>
            <fieldset class="field">
                <legend><?= te('dev.game-form.nhan') ?> <span class="muted"><?= te('dev.game-form.toi_da_10') ?></span></legend>
                <div class="choice-grid"><?php $checks('nhan', $tags, 'ten', $picked['nhan']); ?></div>
            </fieldset>
            <fieldset class="field">
                <legend><?= te('common.platform') ?></legend>
                <div class="choice-grid"><?php $checks('nen_tang', $platforms, 'ten_nen_tang', $picked['nen_tang']); ?></div>
            </fieldset>
            <div class="form-grid">
                <div class="field">
                    <label for="g-age"><?= te('dev.game-form.phan_loai_do_tuoi') ?></label>
                    <input class="input" id="g-age" name="phan_loai_do_tuoi" maxlength="40" value="<?= e((string)$v('phan_loai_do_tuoi')) ?>" placeholder="<?= te('dev.game-form.vi_du_12') ?>">
                </div>
                <div class="field">
                    <label for="g-license"><?= te('game.license') ?></label>
                    <input class="input" id="g-license" name="ten_giay_phep" maxlength="120" value="<?= e((string)$v('ten_giay_phep')) ?>" placeholder="<?= te('dev.game-form.vi_du_gpl_3_cc') ?>">
                </div>
            </div>
            <div class="field">
                <label for="g-url"><?= te('dev.game-form.lien_ket_choi_tren_web') ?></label>
                <input class="input" id="g-url" type="url" name="url_choi_ngoai" maxlength="1000" value="<?= e((string)$v('url_choi_ngoai')) ?>" placeholder="https://">
                <span class="hint"><?= te('dev.game-form.trang_choi_tren_trinh_duyet') ?></span>
            </div>
        </div>
    </section>

    <section class="settings-block" aria-labelledby="vis-h">
        <div class="about"><h2 id="vis-h"><?= te('label.content_status.published') ?></h2><p><?= te('dev.game-form.ban_nhap_chi_tac_gia') ?></p></div>
        <div class="panel form">
            <?php if ($isNew): ?>
                <p class="hint"><?= te('dev.game-form.du_an_duoc_tao_o') ?></p>
            <?php elseif ($statusLocked): ?>
                <p>Trạng thái hiện tại: <?= status_pill('game_status', $game['trang_thai']) ?></p>
                <p class="hint"><?= te('dev.game-form.trang_thai_nay_do_doi') ?></p>
            <?php else: ?>
                <fieldset class="field">
                    <legend><?= te('common.status') ?></legend>
                    <div class="choice-grid">
                        <?php foreach (DevGameController::DEV_STATUSES as $s): ?>
                            <label class="check"><input type="radio" name="trang_thai" value="<?= e($s) ?>"<?= $v('trang_thai', 'draft') === $s ? ' checked' : '' ?>> <span><?= e(label('game_status', $s)) ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            <?php endif; ?>
            <label class="check"><input type="checkbox" name="cho_phep_binh_luan" value="1"<?= $flag('cho_phep_binh_luan') ? ' checked' : '' ?>> <span><?= te('dev.game-form.cho_phep_binh_luan') ?></span></label>
            <label class="check"><input type="checkbox" name="cho_phep_danh_gia" value="1"<?= $flag('cho_phep_danh_gia') ? ' checked' : '' ?>> <span><?= te('dev.game-form.cho_phep_danh_gia') ?></span></label>
        </div>
    </section>

    <div class="form-actions section">
        <button class="btn primary" type="submit"><?= $isNew ? te('dev.create_game') : te('collection.show.luu_thay_doi') ?></button>
        <a class="btn ghost" href="<?= e(app_url($isNew ? '/dev/games' : '/dev/games/' . (int)$game['ma'])) ?>"><?= te('common.cancel') ?></a>
    </div>
</form>
