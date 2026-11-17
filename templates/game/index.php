<?php
/** Catalogue with filters. */
$priceOptions = [
    '' => t('game.index.moi.muc.gia'),
    'free' => t('label.pricing.free'),
    'paid' => t('label.pricing.paid'),
    'under5' => t('game.index.duoi.5'),
    'under15' => t('game.index.duoi.15'),
];
$hasFilters = $filters['q'] !== '' || $filters['category'] !== '' || $filters['tag'] !== '' || $filters['platform'] !== '' || $filters['price'] !== '' || $filters['status'] !== '' || $filters['type'] !== '' || $filters['sale'];
?>
<div class="title-row">
    <div>
        <h1><?= e($heading) ?></h1>
        <p class="sub"><?= te('game.index.ket-qua', [ fmt_int($total) ,  $filters['q'] !== '' ? t('game.index.cho', [$filters['q']]) : '' ]) ?></p>
    </div>
</div>

<form class="toolbar" method="get" action="<?= e(app_url('/games')) ?>">
    <div class="search">
        <label class="sr-only" for="catalogue-q"><?= te('game.index.tim_theo_ten_hoac_the') ?></label>
        <input class="input" id="catalogue-q" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="<?= te('game.index.ten_tro_choi_the') ?>" maxlength="100">
    </div>
    <label class="sr-only" for="f-category"><?= te('common.categories') ?></label>
    <select class="input" id="f-category" name="category" data-autosubmit>
        <option value=""><?= te('game.index.moi_the_loai') ?></option>
        <?php foreach ($categories as $c): ?><option value="<?= e($c['duong_dan_rut_gon']) ?>"<?= $filters['category'] === $c['duong_dan_rut_gon'] ? ' selected' : '' ?>><?= e($c['ten']) ?></option><?php endforeach; ?>
    </select>
    <label class="sr-only" for="f-platform"><?= te('common.platform') ?></label>
    <select class="input" id="f-platform" name="platform" data-autosubmit>
        <option value=""><?= te('label.download_platform.any') ?></option>
        <?php foreach ($platforms as $p): ?><option value="<?= e($p['ma_nen_tang']) ?>"<?= $filters['platform'] === $p['ma_nen_tang'] ? ' selected' : '' ?>><?= e($p['ten_nen_tang']) ?></option><?php endforeach; ?>
    </select>
    <label class="sr-only" for="f-price"><?= te('common.price') ?></label>
    <select class="input" id="f-price" name="price" data-autosubmit>
        <?php foreach ($priceOptions as $value => $text): ?><option value="<?= e($value) ?>"<?= $filters['price'] === $value ? ' selected' : '' ?>><?= e($text) ?></option><?php endforeach; ?>
    </select>
    <label class="sr-only" for="f-status"><?= te('game.index.giai_doan') ?></label>
    <select class="input" id="f-status" name="status" data-autosubmit>
        <option value=""><?= te('game.index.moi_giai_doan') ?></option>
        <?php foreach (GameModel::RELEASE_STATUSES as $s): ?><option value="<?= e($s) ?>"<?= $filters['status'] === $s ? ' selected' : '' ?>><?= e(label('release_status', $s)) ?></option><?php endforeach; ?>
    </select>
    <span class="spacer"></span>
    <label class="sr-only" for="f-sort"><?= te('common.sort') ?></label>
    <select class="input" id="f-sort" name="sort" data-autosubmit>
        <?php foreach (GameModel::sortOptions() as $value => $text): ?><option value="<?= e($value) ?>"<?= $filters['sort'] === $value ? ' selected' : '' ?>><?= e($text) ?></option><?php endforeach; ?>
    </select>
    <?php if ($filters['sale']): ?><input type="hidden" name="sale" value="1"><?php endif; ?>
    <?php if ($filters['tag'] !== ''): ?><input type="hidden" name="tag" value="<?= e($filters['tag']) ?>"><?php endif; ?>
    <?php if ($filters['type'] !== ''): ?><input type="hidden" name="type" value="<?= e($filters['type']) ?>"><?php endif; ?>
    <button class="btn" type="submit"><?= te('common.filter') ?></button>
</form>

<div class="chips chips-row" aria-label="<?= te('game.index.loc_nhanh') ?>">
    <a class="chip" href="<?= e(app_url('/games' . qs(['sale' => $filters['sale'] ? null : 1, 'page' => null]))) ?>" aria-pressed="<?= $filters['sale'] ? 'true' : 'false' ?>"><?= te('home.on_sale') ?></a>
    <a class="chip" href="<?= e(app_url('/games' . qs(['price' => $filters['price'] === 'free' ? null : 'free', 'page' => null]))) ?>" aria-pressed="<?= $filters['price'] === 'free' ? 'true' : 'false' ?>"><?= te('game.price_free') ?></a>
    <a class="chip" href="<?= e(app_url('/games' . qs(['status' => $filters['status'] === 'early_access' ? null : 'early_access', 'page' => null]))) ?>" aria-pressed="<?= $filters['status'] === 'early_access' ? 'true' : 'false' ?>"><?= te('label.release_status.early_access') ?></a>
    <a class="chip" href="<?= e(app_url('/games' . qs(['platform' => $filters['platform'] === 'browser' ? null : 'browser', 'page' => null]))) ?>" aria-pressed="<?= $filters['platform'] === 'browser' ? 'true' : 'false' ?>"><?= te('game.downloads.choi_tren_trinh_duyet') ?></a>
    <?php if ($hasFilters): ?><a class="chip" href="<?= e(app_url('/games')) ?>"><?= te('game.clear_filters') ?></a><?php endif; ?>
</div>

<?php if ($games): ?>
    <div class="games-grid">
        <?php foreach ($games as $game) partial('game-card', ['game' => $game, 'owned' => in_array((int)$game['ma'], $owned, true)]); ?>
    </div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
<?php else: ?>
    <div class="empty">
        <p><?= te('game.index.khong_co_tro_choi_nao') ?></p>
        <a class="btn" href="<?= e(app_url('/games')) ?>"><?= te('game.clear_filters') ?></a>
    </div>
<?php endif; ?>
