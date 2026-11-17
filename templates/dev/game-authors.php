<?php
/** Authors and revenue split. @var array $game @var string $tab @var array $authors @var int $maxAuthors @var bool $isCreator */
$gid = (int)$game['ma'];
$creator = (int)$game['nguoi_tao_id'];
$pct = static fn($v) => rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.');
$sum = array_sum(array_map(static fn($a) => (float)$a['phan_tram_huong_doanh_thu'], $authors));
?>
<?php partial('dev-game-tabs', ['game' => $game, 'tab' => $tab]); ?>

<section class="settings-block" aria-labelledby="authors-h">
    <div class="about">
        <h2 id="authors-h"><?= te('dev.game-authors.tac_gia_va_chia_doanh') ?></h2>
        <p><?= te('dev.game-authors.sau_phi_nen_tang_moi') ?></p>
        <?php if (!$isCreator): ?><p><?= te('dev.game-authors.chi_nguoi_tao_du_an') ?></p><?php endif; ?>
    </div>
    <?php if ($isCreator): ?>
    <form class="panel form" method="post" action="<?= e(app_url('/dev/games/' . $gid . '/authors')) ?>">
        <?= csrf_field() ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th scope="col"><?= te('common.author') ?></th><th scope="col" class="num"><?= te('dev.game-authors.ty_le') ?></th><th scope="col" class="num"><?= te('dev.game-authors.thu_tu') ?></th><th scope="col"><?= te('admin.jam-form.go') ?></th></tr></thead>
                <tbody>
                <?php foreach ($authors as $a): $u = (int)$a['nguoi_dung_id']; ?>
                    <tr>
                        <td><?= e($a['ten_hien_thi'] ?? '') ?><span class="sub">@<?= e($a['ten_dang_nhap'] ?? '') ?><?= $u === $creator ? ' ' . te('dev.game-authors.dot_nguoi_tao') : '' ?><?= !empty($a['chua_luu']) ? ' ' . te('dev.game-authors.dot_chua_luu') : '' ?></span></td>
                        <td class="num"><label class="sr-only" for="sh-<?= $u ?>"><?= te('dev.game-authors.ty-le-cua', [ e($a['ten_hien_thi'] ?? '') ]) ?></label><input class="input sm num" id="sh-<?= $u ?>" name="share[<?= $u ?>]" inputmode="decimal" pattern="\d{1,3}([.,]\d{1,2})?" required value="<?= e(old('share', [])[$u] ?? $pct($a['phan_tram_huong_doanh_thu'])) ?>"></td>
                        <td class="num"><label class="sr-only" for="or-<?= $u ?>"><?= te('dev.game-authors.thu-tu-cua', [ e($a['ten_hien_thi'] ?? '') ]) ?></label><input class="input sm num" id="or-<?= $u ?>" type="number" name="order[<?= $u ?>]" min="0" max="99" value="<?= (int)(old('order', [])[$u] ?? $a['thu_tu_hien_thi']) ?>"></td>
                        <td><?php if ($u !== $creator): ?><label class="check"><input type="checkbox" name="remove[<?= $u ?>]" value="1"<?= !empty(old('remove', [])[$u]) ? ' checked' : '' ?>> <span class="sr-only"><?= te('dev.game-authors.go', [ e($a['ten_hien_thi'] ?? '') ]) ?></span></label><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (count($authors) < $maxAuthors): ?>
        <div class="form-grid">
            <div class="field">
                <label for="a-new"><?= te('dev.game-authors.them_tac_gia_ten_dang') ?></label>
                <input class="input" id="a-new" name="tac_gia_moi" maxlength="50" autocomplete="off" value="<?= e(old('tac_gia_moi')) ?>">
                <span class="hint"><?= te('dev.game-authors.tai_khoan_dang_hoat_dong') ?></span>
            </div>
            <div class="field">
                <label for="a-share"><?= te('dev.game-authors.ty_le_cua_tac_gia') ?></label>
                <input class="input" id="a-share" name="share_moi" inputmode="decimal" pattern="\d{1,3}([.,]\d{1,2})?" value="<?= e(old('share_moi')) ?>">
                <span class="hint"><?= te('dev.game-authors.giam_ty_le_cua_tac') ?></span>
            </div>
        </div>
        <?php endif; ?>
        <p class="hint"><?= te('dev.game-authors.tong-hien-tai-toi-da-tac-gia', [ e($pct($sum)) ,  (int)$maxAuthors ]) ?></p>
        <div class="form-actions"><button class="btn primary" type="submit"><?= te('common.save') ?></button></div>
    </form>
    <?php else: ?>
    <div class="panel">
        <div class="rows">
            <?php foreach ($authors as $a): ?>
                <div class="row"><div class="grow"><?= e($a['ten_hien_thi'] ?? '') ?><span class="sub">@<?= e($a['ten_dang_nhap'] ?? '') ?><?= (int)$a['nguoi_dung_id'] === $creator ? ' ' . te('dev.game-authors.dot_nguoi_tao') : '' ?></span></div><strong><?= e($pct($a['phan_tram_huong_doanh_thu'])) ?>%</strong></div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</section>
