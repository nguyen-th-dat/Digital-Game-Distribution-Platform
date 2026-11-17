<?php
/**
 * Download links of a project.
 * @var array $game @var string $tab @var array $links @var array $counts @var string $status
 * @var array $releases @var int $maxLinks
 */
$gid = (int)$game['ma'];
$base = '/dev/games/' . $gid . '/downloads';
$providerSelect = static function (string $id, string $selected): void {
    echo '<select class="input sm" id="' . e($id) . '" name="nha_cung_cap">';
    echo '<option value=""' . ($selected === '' ? ' selected' : '') . '>Tự nhận diện từ đường dẫn</option>';
    foreach (labels('download_provider') as $k => $t) {
        echo '<option value="' . e($k) . '"' . ($selected === $k ? ' selected' : '') . '>' . e($t) . '</option>';
    }
    echo '</select>';
};
$statusTabs = [
    '' => 'Tất cả',
    'pending' => 'Chờ duyệt',
    'active' => 'Đang hoạt động',
    'hidden' => 'Đã ẩn',
    'rejected' => 'Bị từ chối',
];
?>
<?php partial('dev-game-tabs', ['game' => $game, 'tab' => $tab]); ?>

<div class="grid top">
    <section class="panel span-8" aria-labelledby="dl-h">
        <header>
            <h2 id="dl-h"><?= te('dev.game-downloads.lien_ket_tai_ve') ?></h2>
            <span class="meta"><?= fmt_int((int)($counts['active'] ?? 0)) ?> đang hoạt động<?= ($counts['pending'] ?? 0) > 0 ? ' ' . te('common.dot') . ' ' . fmt_int((int)$counts['pending']) . ' ' . te('dev.game-downloads.cho_duyet') : '' ?></span>
        </header>
        <p class="hint"><?= te('dev.game-downloads.nguoi_choi_da_so_huu') ?></p>
        <p class="hint"><?= te('dev.game-downloads.du_an_cua_ban_cung') ?> <a class="link" href="<?= e(app_url('/dev/games/' . $gid)) ?>"><?= te('dev.game-downloads.lien_ket_choi_tren_web') ?></a> <?= te('dev.game-downloads.de_nguoi_choi_thay_nut') ?></p>

        <nav class="tabs" aria-label="<?= te('dev.game-downloads.loc_lien_ket_tai_ve') ?>">
            <?php foreach ($statusTabs as $key => $text): ?>
                <a href="<?= e(app_url($base . ($key !== '' ? '?status=' . $key : ''))) ?>"<?= $status === $key ? ' aria-selected="true"' : '' ?>><?= e($text) ?><?= isset($counts[$key]) ? '<span class="count">' . fmt_int((int)$counts[$key]) . '</span>' : '' ?></a>
            <?php endforeach; ?>
        </nav>

        <?php if (!$links): ?>
            <div class="empty"><p><?= $status !== '' ? 'Không có liên kết nào ở trạng thái này.' : 'Chưa có liên kết tải về nào. Thêm Google Drive, OneDrive, Dropbox hoặc bất kỳ đường dẫn nào để người chơi tải dự án.' ?></p></div>
        <?php endif; ?>
        <div class="rows">
            <?php foreach ($links as $l): $lid = (int)$l['ma']; ?>
                <div class="row" id="download-<?= $lid ?>">
                    <div class="grow">
                        <strong><?= e($l['ten_hien_thi']) ?></strong>
                        <?= status_pill('download_provider', $l['nha_cung_cap']) ?>
                        <?= status_pill('download_platform', $l['he_dieu_ke']) ?>
                        <?= status_pill('download_link_status', $l['trang_thai']) ?>
                        <span class="sub">
                            <?= $l['ten_phien_ban'] ? te('game.version') . ' ' . e($l['ten_phien_ban']) . ' (' . e(label('channel', $l['kenh_phat_hanh'])) . ')' : te('dev.game-downloads.khong_gan_phien_ban') ?>
                            <?= $l['dung_luong'] ? ' ' . te('common.dot') . ' ' . e($l['dung_luong']) : '' ?>
                            · <?= fmt_int((int)$l['so_lan_tai']) ?> lượt tải
                            · thứ tự <?= (int)$l['thu_tu_sap_xep'] ?>
                            <?= $l['nguoi_dong_gop'] ? ' ' . te('dev.game-downloads.dot_gui_boi') . ' ' . e($l['nguoi_dong_gop']) : '' ?>
                        </span>
                        <?php if ($l['ghi_chu']): ?><span class="sub"><?= e($l['ghi_chu']) ?></span><?php endif; ?>
                        <?php if ($l['ly_do_tu_choi']): ?><span class="sub neg-text">Lý do từ chối: <?= e($l['ly_do_tu_choi']) ?></span><?php endif; ?>
                        <span class="sub"><a class="link" href="<?= e($l['url_tai']) ?>" target="_blank" rel="noopener noreferrer"><?= e(excerpt($l['url_tai'], 80)) ?></a></span>
                    </div>
                    <div class="cluster">
                        <?php if ($l['trang_thai'] === 'pending'): ?>
                            <form method="post" action="<?= e(app_url($base . '/' . $lid . '/approve')) ?>">
                                <?= csrf_field() ?><button class="btn sm primary" type="submit"><?= te('dev.game-downloads.duyet') ?></button>
                            </form>
                            <details class="edit">
                                <summary class="link-btn"><?= te('label.refund_status.rejected') ?></summary>
                                <form class="form well" method="post" action="<?= e(app_url($base . '/' . $lid . '/reject')) ?>">
                                    <?= csrf_field() ?>
                                    <div class="field"><label for="rj-<?= $lid ?>"><?= te('admin.reason') ?></label><input class="input sm" id="rj-<?= $lid ?>" name="ly_do" maxlength="500" required placeholder="<?= te('dev.game-downloads.vi_du_link_khong_tai') ?>"></div>
                                    <button class="btn sm primary" type="submit"><?= te('label.refund_status.rejected') ?></button>
                                </form>
                            </details>
                        <?php endif; ?>
                        <details class="edit">
                            <summary class="link-btn"><?= te('common.edit') ?></summary>
                            <form class="form well" method="post" action="<?= e(app_url($base . '/' . $lid)) ?>">
                                <?= csrf_field() ?>
                                <div class="field"><label for="du-<?= $lid ?>"><?= te('settings.display_name') ?></label><input class="input sm" id="du-<?= $lid ?>" name="ten_hien_thi" maxlength="200" value="<?= e($l['ten_hien_thi']) ?>"></div>
                                <div class="field"><label for="dp-<?= $lid ?>"><?= te('dev.game-downloads.noi_luu_tru') ?></label><?php $providerSelect('dp-' . $lid, $l['nha_cung_cap']); ?></div>
                                <div class="field"><label for="dk-<?= $lid ?>"><?= te('dev.game-downloads.duong_dan_tai_ve') ?></label><input class="input sm" id="dk-<?= $lid ?>" type="url" name="url_tai" maxlength="1500" required value="<?= e($l['url_tai']) ?>"></div>
                                <div class="field"><label for="dh-<?= $lid ?>"><?= te('dev.game-downloads.he_dieu_hanh') ?></label>
                                    <select class="input sm" id="dh-<?= $lid ?>" name="he_dieu_ke">
                                        <?php foreach (labels('download_platform') as $k => $t): ?>
                                            <option value="<?= e($k) ?>"<?= $l['he_dieu_ke'] === $k ? ' selected' : '' ?>><?= e($t) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="field"><label for="dr-<?= $lid ?>"><?= te('dev.release_version') ?></label>
                                    <select class="input sm" id="dr-<?= $lid ?>" name="phien_ban_id">
                                        <option value="0"><?= te('dev.game-downloads.khong_gan') ?></option>
                                        <?php foreach ($releases as $r): ?>
                                            <option value="<?= (int)$r['ma'] ?>"<?= (int)$l['phien_ban_id'] === (int)$r['ma'] ? ' selected' : '' ?>><?= e($r['ten_phien_ban']) ?> (<?= e(label('channel', $r['kenh_phat_hanh'])) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="field"><label for="ds-<?= $lid ?>"><?= te('download.size') ?></label><input class="input sm" id="ds-<?= $lid ?>" name="dung_luong" maxlength="40" value="<?= e((string)$l['dung_luong']) ?>" placeholder="<?= te('dev.game-downloads.vi_du_850_mb') ?>"></div>
                                <div class="field"><label for="dn-<?= $lid ?>"><?= te('common.note') ?></label><input class="input sm" id="dn-<?= $lid ?>" name="ghi_chu" maxlength="500" value="<?= e((string)$l['ghi_chu']) ?>"></div>
                                <div class="field"><label for="do-<?= $lid ?>"><?= te('dev.game-authors.thu_tu') ?></label><input class="input sm" id="do-<?= $lid ?>" type="number" name="thu_tu_sap_xep" min="0" max="1000" value="<?= (int)$l['thu_tu_sap_xep'] ?>"></div>
                                <label class="check"><input type="checkbox" name="trang_thai" value="active"<?= $l['trang_thai'] === 'active' ? ' checked' : '' ?>> <span><?= te('dev.game-downloads.hien_voi_nguoi_choi') ?></span></label>
                                <button class="btn sm primary" type="submit"><?= te('common.save') ?></button>
                            </form>
                        </details>
                        <form method="post" action="<?= e(app_url($base . '/' . $lid . '/delete')) ?>" data-confirm="Xóa liên kết tải về này?">
                            <?= csrf_field() ?><button class="link-btn neg" type="submit"><?= te('admin.delete') ?></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if (count($links) < $maxLinks): ?>
    <section class="panel span-4" id="add-download" aria-labelledby="add-dl-h">
        <header><h2 id="add-dl-h"><?= te('download.add_link') ?></h2></header>
        <form class="form" method="post" action="<?= e(app_url($base)) ?>">
            <?= csrf_field() ?>
            <div class="field"><label for="m-title"><?= te('settings.display_name') ?></label><input class="input" id="m-title" name="ten_hien_thi" maxlength="200" value="<?= e(old('ten_hien_thi')) ?>" placeholder="<?= te('dev.game-downloads.bo_trong_dung_ten_noi') ?>"><span class="hint"><?= te('dev.game-downloads.vi_du_ban_windows_1') ?></span></div>
            <div class="field"><label for="m-provider"><?= te('dev.game-downloads.noi_luu_tru') ?></label><?php $providerSelect('m-provider', (string)old('nha_cung_cap', '')); ?><span class="hint"><?= te('dev.game-downloads.de_trong_de_he_thong') ?></span></div>
            <div class="field"><label for="m-url"><?= te('dev.game-downloads.duong_dan_tai_ve') ?></label><input class="input" id="m-url" type="url" name="url_tai" maxlength="1500" required placeholder="https://" value="<?= e(old('url_tai')) ?>"><span class="hint"><?= te('dev.game-downloads.google_drive_onedrive_dropbox_mediafire') ?></span></div>
            <div class="field"><label for="m-platform"><?= te('dev.game-downloads.he_dieu_hanh') ?></label>
                <select class="input" id="m-platform" name="he_dieu_ke">
                    <?php foreach (labels('download_platform') as $k => $t): ?>
                        <option value="<?= e($k) ?>"<?= old('he_dieu_ke', 'any') === $k ? ' selected' : '' ?>><?= e($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label for="m-release"><?= te('dev.release_version') ?></label>
                <select class="input" id="m-release" name="phien_ban_id">
                    <option value="0"><?= te('dev.game-downloads.khong_gan') ?></option>
                    <?php foreach ($releases as $r): ?>
                        <option value="<?= (int)$r['ma'] ?>"<?= (int)old('phien_ban_id', 0) === (int)$r['ma'] ? ' selected' : '' ?>><?= e($r['ten_phien_ban']) ?> (<?= e(label('channel', $r['kenh_phat_hanh'])) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <span class="hint"><?= te('dev.game-downloads.gan_phien_ban_de_nguoi') ?></span>
            </div>
            <div class="field"><label for="m-size"><?= te('download.size') ?></label><input class="input" id="m-size" name="dung_luong" maxlength="40" value="<?= e(old('dung_luong')) ?>" placeholder="<?= te('dev.game-downloads.vi_du_850_mb') ?>"></div>
            <div class="field"><label for="m-note"><?= te('common.note') ?></label><input class="input" id="m-note" name="ghi_chu" maxlength="500" value="<?= e(old('ghi_chu')) ?>" placeholder="<?= te('dev.game-downloads.vi_du_can_giai_nen') ?>"></div>
            <div class="field"><label for="m-order"><?= te('dev.game-authors.thu_tu') ?></label><input class="input" id="m-order" type="number" name="thu_tu_sap_xep" min="0" max="1000" value="<?= e(old('thu_tu_sap_xep', (string)count($links))) ?>"></div>
            <div class="form-actions"><button class="btn primary" type="submit"><?= te('common.more') ?></button></div>
        </form>
    </section>
    <?php endif; ?>
</div>
