<?php
/** Project releases. @var array $game @var string $tab @var array $releases */
$gid = (int)$game['ma'];
$base = '/dev/games/' . $gid . '/releases';
$isPublic = in_array($game['trang_thai'], ['published', 'unlisted'], true);
$channelSelect = static function (string $id, string $selected): void {
    echo '<select class="input sm" id="' . e($id) . '" name="kenh_phat_hanh" required>';
    foreach (labels('channel') as $k => $t) {
        echo '<option value="' . e($k) . '"' . ($selected === $k ? ' selected' : '') . '>' . e($t) . '</option>';
    }
    echo '</select>';
};
?>
<?php partial('dev-game-tabs', ['game' => $game, 'tab' => $tab]); ?>

<div class="grid top">
    <section class="panel span-8" aria-labelledby="rel-h">
        <header><h2 id="rel-h"><?= te('dev.game-releases.lich_su_phien_ban') ?></h2><span class="meta"><?= te('dev.game-releases.phien-ban', [ count($releases) ]) ?></span></header>
        <?php if (!$releases): ?><div class="empty"><p><?= te('dev.game-releases.chua_co_phien_ban_nao') ?></p></div><?php endif; ?>
        <div class="rows">
            <?php foreach ($releases as $r): $rid = (int)$r['ma']; ?>
                <div class="row" id="release-<?= $rid ?>">
                    <div class="grow">
                        <strong><?= e($r['ten_phien_ban']) ?></strong> <?= status_pill('channel', $r['kenh_phat_hanh']) ?><?= (int)$r['cong_khai'] ? '' : ' <span class="pill">' . e(t('dev.game-releases.an')) . '</span>' ?>
                        <span class="sub"><?= e(fmt_datetime($r['ngay_phat_hanh'])) ?><?= strtotime($r['ngay_phat_hanh']) > time() ? ' ' . '(' . te('dev.game-releases.da_len_lich') . ')' : '' ?> · <?= e($r['ten_hien_thi']) ?></span>
                        <?php if ($r['ghi_chu_phat_hanh']): ?><div class="prose small"><?= e_multiline(excerpt($r['ghi_chu_phat_hanh'], 280)) ?></div><?php endif; ?>
                    </div>
                    <div class="cluster">
                        <details class="edit">
                            <summary class="link-btn"><?= te('common.edit') ?></summary>
                            <form class="form well" method="post" action="<?= e(app_url($base . '/' . $rid)) ?>">
                                <?= csrf_field() ?>
                                <div class="field"><label for="rv-<?= $rid ?>"><?= te('dev.release_version') ?></label><input class="input sm" id="rv-<?= $rid ?>" name="ten_phien_ban" maxlength="60" required value="<?= e($r['ten_phien_ban']) ?>"></div>
                                <div class="field"><label for="rc-<?= $rid ?>"><?= te('download.channel') ?></label><?php $channelSelect('rc-' . $rid, $r['kenh_phat_hanh']); ?></div>
                                <div class="field"><label for="rd-<?= $rid ?>"><?= te('dev.game-releases.ngay_phat_hanh') ?></label><input class="input sm" id="rd-<?= $rid ?>" type="datetime-local" name="ngay_phat_hanh" required value="<?= e(datetime_local($r['ngay_phat_hanh'])) ?>"></div>
                                <div class="field"><label for="rn-<?= $rid ?>"><?= te('dev.release_notes') ?></label><textarea class="input" id="rn-<?= $rid ?>" name="ghi_chu_phat_hanh" maxlength="20000"><?= e((string)$r['ghi_chu_phat_hanh']) ?></textarea></div>
                                <label class="check"><input type="checkbox" name="cong_khai" value="1"<?= (int)$r['cong_khai'] ? ' checked' : '' ?>> <span><?= te('collection.public') ?></span></label>
                                <button class="btn sm primary" type="submit"><?= te('common.save') ?></button>
                            </form>
                        </details>
                        <form method="post" action="<?= e(app_url($base . '/' . $rid . '/delete')) ?>" data-confirm="<?= te('dev.game-releases.xoa-phien-ban-e-r-ten-phien-ban') ?>"><?= csrf_field() ?><button class="link-btn neg" type="submit"><?= te('admin.delete') ?></button></form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel span-4" id="add-release" aria-labelledby="add-rel-h">
        <header><h2 id="add-rel-h"><?= te('dev.game-releases.them_phien_ban') ?></h2></header>
        <form class="form" method="post" action="<?= e(app_url($base)) ?>">
            <?= csrf_field() ?>
            <div class="field"><label for="r-ver"><?= te('dev.release_version') ?></label><input class="input" id="r-ver" name="ten_phien_ban" maxlength="60" required placeholder="<?= te('dev.game-releases.vi_du_1_2') ?>" value="<?= e(old('ten_phien_ban')) ?>"></div>
            <div class="field"><label for="r-ch"><?= te('download.channel') ?></label><?php $channelSelect('r-ch', old('kenh_phat_hanh', 'stable')); ?></div>
            <div class="field"><label for="r-date"><?= te('dev.game-releases.ngay_phat_hanh') ?></label><input class="input" id="r-date" type="datetime-local" name="ngay_phat_hanh" value="<?= e(old('ngay_phat_hanh')) ?>"><span class="hint"><?= te('dev.game-releases.bo_trong_phat_hanh_ngay') ?></span></div>
            <div class="field"><label for="r-notes"><?= te('dev.release_notes') ?></label><textarea class="input" id="r-notes" name="ghi_chu_phat_hanh" maxlength="20000"><?= e(old('ghi_chu_phat_hanh')) ?></textarea></div>
            <label class="check"><input type="checkbox" name="cong_khai" value="1"<?= old('ten_phien_ban', null) === null || old('cong_khai') !== '' ? ' checked' : '' ?>> <span><?= te('collection.public') ?><span class="sub"><?= $isPublic ? te('dev.game-releases.nguoi_theo_doi_nhan_thong') : te('dev.game-releases.du_an_dang_la_ban') ?></span></span></label>
            <div class="form-actions"><button class="btn primary" type="submit"><?= te('common.more') ?></button></div>
        </form>
    </section>
</div>
