<?php
/** Project images and links. @var array $game @var string $tab @var array $media @var int $maxMedia */
$gid = (int)$game['ma'];
$base = '/dev/games/' . $gid . '/media';
$isImage = static fn(string $type) => in_array($type, ['cover', 'thumbnail', 'screenshot', 'other'], true);
$taken = array_column($media, 'loai_media');
?>
<?php partial('dev-game-tabs', ['game' => $game, 'tab' => $tab]); ?>

<div class="grid top">
    <section class="panel span-8" aria-labelledby="media-h">
        <header><h2 id="media-h"><?= te('dev.game-media.anh_va_lien_ket') ?></h2><span class="meta"><?= count($media) ?>/<?= (int)$maxMedia ?></span></header>
        <p class="hint"><?= te('dev.game-media.anh_bia_va_anh_chup') ?></p>
        <?php if (!$media): ?><div class="empty"><p><?= te('dev.game-media.chua_co_anh_nao_them') ?></p></div><?php endif; ?>
        <div class="rows">
            <?php foreach ($media as $m): $mid = (int)$m['ma']; $image = $isImage($m['loai_media']); ?>
                <div class="row" id="media-<?= $mid ?>">
                    <div class="cell-game grow">
                        <?php if ($image): ?><img src="<?= e(app_media_url($m['url_media'])) ?>" alt="<?= e($m['van_ban_thay_the'] ?: '') ?>" loading="lazy" data-fallback=""><?php else: ?><span class="thumb"></span><?php endif; ?>
                        <div>
                            <strong><?= e(label('media_type', $m['loai_media'])) ?></strong> <span class="muted">· thứ tự <?= (int)$m['thu_tu_sap_xep'] ?></span>
                            <span class="sub"><?= $m['chu_thich'] !== null && $m['chu_thich'] !== '' ? media_credit_html($m['chu_thich']) : 'Chưa có chú thích' ?></span>
                            <?php if (!$image): ?><span class="sub"><a class="link" href="<?= e($m['url_media']) ?>" target="_blank" rel="noopener noreferrer"><?= e(excerpt($m['url_media'], 60)) ?></a></span><?php endif; ?>
                        </div>
                    </div>
                    <div class="cluster">
                        <details class="edit">
                            <summary class="link-btn"><?= te('common.edit') ?></summary>
                            <form class="form well" method="post" action="<?= e(app_url($base . '/' . $mid)) ?>">
                                <?= csrf_field() ?>
                                <?php if (!$image): ?>
                                    <div class="field"><label for="mu-<?= $mid ?>"><?= te('dev.game-media.dia_chi') ?></label><input class="input sm" id="mu-<?= $mid ?>" type="url" name="url_media" maxlength="1500" required value="<?= e($m['url_media']) ?>"></div>
                                <?php endif; ?>
                                <div class="field"><label for="mc-<?= $mid ?>"><?= te('dev.game-media.chu_thich') ?></label><input class="input sm" id="mc-<?= $mid ?>" name="chu_thich" maxlength="300" value="<?= e((string)$m['chu_thich']) ?>"></div>
                                <div class="field"><label for="ma-<?= $mid ?>"><?= te('dev.game-media.van_ban_thay_the') ?></label><input class="input sm" id="ma-<?= $mid ?>" name="van_ban_thay_the" maxlength="300"<?= $image ? ' required' : '' ?> value="<?= e((string)$m['van_ban_thay_the']) ?>"></div>
                                <div class="field"><label for="mo-<?= $mid ?>"><?= te('dev.game-authors.thu_tu') ?></label><input class="input sm" id="mo-<?= $mid ?>" type="number" name="thu_tu_sap_xep" min="0" max="1000" value="<?= (int)$m['thu_tu_sap_xep'] ?>"></div>
                                <button class="btn sm primary" type="submit"><?= te('common.save') ?></button>
                            </form>
                        </details>
                        <form method="post" action="<?= e(app_url($base . '/' . $mid . '/delete')) ?>" data-confirm="Xóa <?= e(mb_strtolower(label('media_type', $m['loai_media']))) ?> này?<?= $image ? ' ' . te('dev.game-media.tep_anh_da_tai_len') : "" ?>"><?= csrf_field() ?><button class="link-btn neg" type="submit"><?= te('admin.delete') ?></button></form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if (count($media) < $maxMedia): ?>
    <section class="panel span-4" id="add-media" aria-labelledby="add-media-h">
        <header><h2 id="add-media-h"><?= te('common.more') ?></h2></header>
        <form class="form" method="post" action="<?= e(app_url($base)) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="field"><label for="m-type"><?= te('common.type') ?></label>
                <select class="input" id="m-type" name="loai_media" required>
                    <option value=""><?= te('dev.game-media.chon') ?></option>
                    <?php foreach (labels('media_type') as $k => $t): if (in_array($k, ['cover', 'thumbnail'], true) && in_array($k, $taken, true)) continue; ?>
                        <option value="<?= e($k) ?>"<?= old('loai_media') === $k ? ' selected' : '' ?>><?= e($t) ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="hint"><?= te('dev.game-media.moi_du_an_co_mot') ?></span>
            </div>
            <div class="field"><label for="m-file"><?= te('dev.game-media.tep_anh') ?></label><input class="input" id="m-file" type="file" name="tep" accept="image/jpeg,image/png,image/webp,image/gif"><span class="hint"><?= te('dev.game-media.cho_anh_bia_anh_thu') ?></span></div>
            <div class="field"><label for="m-url"><?= te('dev.game-media.dia_chi_lien_ket') ?></label><input class="input" id="m-url" type="url" name="url_media" maxlength="1500" placeholder="https://" value="<?= e(old('url_media')) ?>"><span class="hint"><?= te('dev.game-media.cho_doan_gioi_thieu_trailer') ?></span></div>
            <div class="field"><label for="m-cap"><?= te('dev.game-media.chu_thich') ?></label><input class="input" id="m-cap" name="chu_thich" maxlength="300" value="<?= e(old('chu_thich')) ?>"><span class="hint"><?= te('dev.game-media.ghi_nguon_anh_neu_khong') ?></span></div>
            <div class="field"><label for="m-alt"><?= te('dev.game-media.van_ban_thay_the') ?></label><input class="input" id="m-alt" name="van_ban_thay_the" maxlength="300" value="<?= e(old('van_ban_thay_the')) ?>"><span class="hint"><?= te('dev.game-media.mo_ta_noi_dung_anh') ?></span></div>
            <div class="field"><label for="m-order"><?= te('dev.game-authors.thu_tu') ?></label><input class="input" id="m-order" type="number" name="thu_tu_sap_xep" min="0" max="1000" value="<?= e(old('thu_tu_sap_xep', (string)count($media))) ?>"></div>
            <div class="form-actions"><button class="btn primary" type="submit"><?= te('common.more') ?></button></div>
        </form>
    </section>
    <?php endif; ?>
</div>
