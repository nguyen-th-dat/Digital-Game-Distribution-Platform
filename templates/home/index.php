<?php
/** Store home. */
$shelf = function (string $title, array $games, string $more, string $meta = '') use ($owned) {
    if (!$games) return;
    ?>
    <section class="section">
        <div class="section-head">
            <h2><?= e($title) ?></h2>
            <a class="meta link" href="<?= e(app_url($more)) ?>"><?= te('home.index.xem-tat-ca', [ $meta !== '' ? t('home.index.dot', [$meta]) : '' ]) ?></a>
        </div>
        <div class="shelf">
            <?php foreach ($games as $game) partial('game-card', ['game' => $game, 'owned' => in_array((int)$game['ma'], $owned, true)]); ?>
        </div>
    </section>
    <?php
};
?>
<div class="title-row">
    <div>
        <h1><?= te('home.title') ?></h1>
        <p class="sub"><?= te('home.index.tro-choi-doc-lap-dang-duoc-cong-bo', [ fmt_int($totalGames) ]) ?></p>
    </div>
    <div class="actions">
        <a class="btn" href="<?= e(app_url('/games?sale=1')) ?>"><?= te('home.on_sale') ?></a>
        <a class="btn primary" href="<?= e(app_url('/games')) ?>"><?= te('home.explore_all') ?></a>
    </div>
</div>

<?php if ($featured): $isOwned = in_array((int)$featured['ma'], $owned, true); $hero = $featuredShots[0]; ?>
    <section class="feature" aria-label="<?= te('home.featured') ?>">
        <figure class="feature-art">
            <a href="<?= e(game_url($featured)) ?>"><img src="<?= e(app_media_url($hero['url_media'])) ?>" alt="<?= e($hero['van_ban_thay_the'] ?: $featured['tieu_de']) ?>" data-fallback="<?= e($featured['tieu_de']) ?>"></a>
            <?php if ($hero['chu_thich']): ?><figcaption><?= media_credit_html($hero['chu_thich']) ?></figcaption><?php endif; ?>
        </figure>
        <div class="feature-info">
            <h2><a href="<?= e(game_url($featured)) ?>"><?= e($featured['tieu_de']) ?></a></h2>
            <?php if ($featured['nha_phat_trien']): ?><p class="small muted"><?= e($featured['nha_phat_trien']) ?></p><?php endif; ?>
            <?php if ($featured['mo_ta_ngan']): ?><p><?= e($featured['mo_ta_ngan']) ?></p><?php endif; ?>
            <?php if (count($featuredShots) > 1): ?>
                <div class="feature-thumbs">
                    <?php foreach (array_slice($featuredShots, 1) as $shot): ?>
                        <img src="<?= e(app_media_url($shot['url_media'])) ?>" alt="<?= e($shot['van_ban_thay_the'] ?: te('home.index.anh_chup') . ' ' . $featured['tieu_de']) ?>" loading="lazy" data-fallback="">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="cluster small muted">
                <span><?= e(label('release_status', $featured['trang_thai_phat_hanh'])) ?></span>
                <?php if ($featured['the_loai']): ?><span><?= e($featured['the_loai']) ?></span><?php endif; ?>
                <?php if ($featured['so_danh_gia']): ?><span><?= te('home.index.danh-gia', [ stars((float)$featured['diem_tb']) ,  fmt_int($featured['so_danh_gia']) ]) ?></span><?php endif; ?>
            </div>
            <div class="foot">
                <?= price_html($featured, $isOwned) ?>
                <a class="btn primary" href="<?= e(game_url($featured)) ?>"><?= $isOwned ? te('home.open_game') : te('home.view_details') ?></a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php $shelf(te('home.new_releases'), $newest, '/games?sort=new'); ?>
<?php $shelf(te('home.on_sale'), $onSale, '/games?sale=1'); ?>

<div class="grid top section">
    <section class="panel span-7">
        <header><h2><?= te('home.most_owned') ?></h2><a class="meta link" href="<?= e(app_url('/games?sort=popular')) ?>"><?= te('home.ranking') ?></a></header>
        <?php if ($popular): ?>
            <div class="rank-list">
                <?php foreach (array_slice($popular, 0, 6) as $i => $game): ?>
                    <a class="rank-row row-link" href="<?= e(game_url($game)) ?>">
                        <span class="n"><?= $i + 1 ?></span>
                        <?php if ($game['anh_bia']): ?>
                            <img src="<?= e(app_media_url($game['anh_bia'])) ?>" alt="" loading="lazy" data-fallback="">
                        <?php else: ?><span class="thumb"></span><?php endif; ?>
                        <span class="rank-text">
                            <span class="t"><?= e($game['tieu_de']) ?></span>
                            <span class="sub"><?= te('home.index.nguoi-so-huu', [ fmt_int($game['so_huu']) ,  $game['the_loai'] ? ' ' . te('common.dot') . ' ' . e($game['the_loai']) : '' ]) ?></span>
                        </span>
                        <?= price_html($game, in_array((int)$game['ma'], $owned, true)) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty"><?= te('home.no_games') ?></div>
        <?php endif; ?>
    </section>
    <section class="panel span-5">
        <header><h2><?= te('common.categories') ?></h2></header>
        <div class="chips">
            <?php foreach ($categories as $cat): ?>
                <a class="chip" href="<?= e(app_url('/games?category=' . rawurlencode($cat['duong_dan_rut_gon']))) ?>"><?= e($cat['ten']) ?> <span class="faint"><?= fmt_int($cat['so_game']) ?></span></a>
            <?php endforeach; ?>
        </div>
        <?php if ($jams): ?>
            <h3 class="sub-head">Game jam</h3>
            <div class="rows">
                <?php foreach ($jams as $jam): ?>
                    <a class="row row-link" href="<?= e(app_url('/jams/' . rawurlencode($jam['duong_dan_rut_gon']))) ?>">
                        <span class="grow"><?= e($jam['tieu_de']) ?><span class="sub"><?= te('home.index.han-nop-bai-du-thi', [ e(fmt_datetime($jam['han_nop_bai'])) ,  fmt_int($jam['so_bai']) ]) ?></span></span>
                        <?= status_pill('jam_status', $jam['trang_thai']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php $shelf(te('home.top_rated'), $topRated, '/games?sort=rating'); ?>
<?php $shelf(te('price.free'), $free, '/games?price=free'); ?>

<?php if ($bundles): ?>
    <section class="section">
        <div class="section-head"><h2><?= te('bundle.bundles') ?></h2><a class="meta link" href="<?= e(app_url('/bundles')) ?>"><?= te('common.see_all') ?></a></div>
        <div class="games-grid">
            <?php foreach ($bundles as $b): ?>
                <a class="game-card" href="<?= e(app_url('/bundles/' . rawurlencode($b['duong_dan_rut_gon']))) ?>">
                    <div class="cover">
                        <?php if ($b['anh_bia']): ?><img src="<?= e(app_media_url($b['anh_bia'])) ?>" alt="" loading="lazy" data-fallback="<?= e($b['tieu_de']) ?>"><?php else: ?><div class="cover-fallback"><?= e($b['tieu_de']) ?></div><?php endif; ?>
                    </div>
                    <h3><?= e($b['tieu_de']) ?></h3>
                    <div class="meta-line">
                        <span class="by"><?= te('home.index.tro-choi', [ fmt_int($b['so_game']) ]) ?></span>
                        <span class="price"><?php if ((float)$b['tong_gia_le'] > (float)$b['gia']): ?><span class="was"><?= e(money($b['tong_gia_le'])) ?></span><?php endif; ?><span<?= (float)$b['gia'] <= 0 ? ' class="free"' : '' ?>><?= (float)$b['gia'] <= 0 ? te('price.free') : e(money($b['gia'])) ?></span></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
