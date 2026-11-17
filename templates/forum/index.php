<?php
/** Forum hub. @var array $groups @var array $myGames @var array $jamsWithoutBoard */
$user = current_user();
$staff = is_staff();
$titles = ['platform' => te('forum.index.thao_luan_chung'), 'game' => te('forum.index.theo_tro_choi'), 'jam' => te('forum.index.theo_cuoc_thi_lam_game')];
$empty = ['platform' => te('forum.index.chua_co_dien_dan_chung'), 'game' => te('forum.index.chua_tro_choi_nao_mo'), 'jam' => te('forum.index.chua_cuoc_thi_nao_co')];
$canCreate = $staff || $myGames;
?>
<div class="title-row">
    <div>
        <h1><?= te('forum.forums') ?></h1>
        <p class="sub"><?= te('forum.index.hoi_dap_gop_y_va') ?></p>
    </div>
    <?php if ($canCreate): ?><a class="btn" href="#new"><?= te('forum.index.mo_dien_dan') ?></a><?php endif; ?>
</div>

<?php foreach ($groups as $kind => $boards): ?>
<section class="section" aria-labelledby="g-<?= e($kind) ?>">
    <div class="section-head"><h2 id="g-<?= e($kind) ?>"><?= e($titles[$kind]) ?></h2><span class="meta"><?= te('forum.index.dien-dan', [ fmt_int(count($boards)) ]) ?></span></div>
    <?php if ($boards): ?>
        <div class="panel">
            <div class="rows">
                <?php foreach ($boards as $b): ?>
                    <div class="row board-row">
                        <div class="grow">
                            <a class="link t" href="<?= e(app_url('/forums/' . (int)$b['ma'])) ?>"><?= e($b['tieu_de']) ?></a>
                            <?php if (!(int)$b['cong_khai']): ?> <span class="pill"><?= te('collection.private') ?></span><?php endif; ?>
                            <?php if ($kind === 'game' && $b['game_status'] !== 'published'): ?> <span class="pill warn"><?= e(label('game_status', $b['game_status'])) ?></span><?php endif; ?>
                            <span class="sub">
                                <?php if ($kind === 'game'): ?><?= te('forum.index.tro-choi', [ e($b['game_title']) ]) ?><?php elseif ($kind === 'jam'): ?><?= te('forum.index.cuoc-thi', [ e($b['jam_title']) ]) ?><?php endif; ?>
                                <?= $kind !== 'platform' && $b['mo_ta'] ? ' ' . te('common.dot') . ' ' : '' ?><?= $b['mo_ta'] ? e(excerpt($b['mo_ta'], 140)) : '' ?>
                            </span>
                        </div>
                        <div class="board-stats">
                            <span><strong><?= fmt_int((int)$b['so_chu_de']) ?></strong> <?= te('forum.index.chu_de') ?></span>
                            <span><strong><?= fmt_int((int)$b['so_bai']) ?></strong> <?= te('forum.index.tra_loi') ?></span>
                            <span class="faint"><?= $b['hoat_dong_cuoi'] ? e(time_ago($b['hoat_dong_cuoi'])) : te('forum.index.chua_co_bai') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="empty"><p><?= e($empty[$kind]) ?></p></div>
    <?php endif; ?>
</section>
<?php endforeach; ?>

<?php if ($canCreate): ?>
<section class="panel section narrow-panel" id="new" aria-labelledby="new-h">
    <header><h2 id="new-h"><?= te('forum.index.mo_dien_dan_moi') ?></h2></header>
    <form class="form form-grid" method="post" action="<?= e(app_url('/forums')) ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label for="f-kind"><?= te('common.type') ?></label>
            <select class="input" id="f-kind" name="loai_dien_dan" required>
                <?php if ($myGames): ?><option value="game"<?= old('loai_dien_dan') === 'game' ? ' selected' : '' ?>><?= te('forum.index.cho_tro_choi_cua_toi') ?></option><?php endif; ?>
                <?php if ($staff): ?><option value="platform"<?= old('loai_dien_dan') === 'platform' ? ' selected' : '' ?>><?= te('forum.index.dien_dan_chung') ?></option><?php endif; ?>
                <?php if ($staff && $jamsWithoutBoard): ?><option value="jam"<?= old('loai_dien_dan') === 'jam' ? ' selected' : '' ?>><?= te('forum.index.cho_cuoc_thi') ?></option><?php endif; ?>
            </select>
        </div>
        <?php if ($myGames): ?>
        <div class="field">
            <label for="f-game"><?= te('forum.index.tro_choi_khi_chon_loai') ?></label>
            <select class="input" id="f-game" name="tro_choi_id">
                <?php foreach ($myGames as $g): ?><option value="<?= (int)$g['ma'] ?>"<?= (int)old('tro_choi_id') === (int)$g['ma'] ? ' selected' : '' ?>><?= e($g['tieu_de']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <?php if ($staff && $jamsWithoutBoard): ?>
        <div class="field">
            <label for="f-jam"><?= te('forum.index.cuoc_thi_khi_chon_loai') ?></label>
            <select class="input" id="f-jam" name="cuoc_thi_id">
                <?php foreach ($jamsWithoutBoard as $j): ?><option value="<?= (int)$j['ma'] ?>"<?= (int)old('cuoc_thi_id') === (int)$j['ma'] ? ' selected' : '' ?>><?= e($j['tieu_de']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="field full">
            <label for="f-title"><?= te('forum.index.ten_dien_dan') ?></label>
            <input class="input" id="f-title" name="tieu_de" maxlength="180" value="<?= e(old('tieu_de')) ?>" placeholder="<?= te('forum.index.bo_trong_de_dung_ten') ?>">
        </div>
        <div class="field full">
            <label for="f-desc"><?= te('collection.index.mo_ta_khong_bat_buoc') ?></label>
            <textarea class="input" id="f-desc" name="mo_ta" maxlength="1000"><?= e(old('mo_ta')) ?></textarea>
        </div>
        <label class="check full"><input type="checkbox" name="cong_khai" value="1" checked> <?= te('forum.index.cong_khai_cho_moi_nguoi') ?></label>
        <div class="form-actions full"><button class="btn primary" type="submit"><?= te('forum.index.mo_dien_dan') ?></button></div>
    </form>
</section>
<?php endif; ?>
