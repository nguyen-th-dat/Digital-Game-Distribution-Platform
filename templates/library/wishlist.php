<?php
/** Wishlist. @var array $games @var array $owned @var array $inCart @var int $libCount */
?>
<div class="title-row">
    <div>
        <h1><?= te('library.wishlist.danh_sach_mong_muon') ?></h1>
        <p class="sub"><?= te('library.wishlist.tro-choi-gia-giam-theo-chien-dich-duoc-cap-nhat-tu-dong', [ fmt_int($total) ]) ?></p>
    </div>
</div>

<nav class="tabs" aria-label="<?= te('library.title') ?>">
    <a href="<?= e(app_url('/library')) ?>" aria-selected="false"><?= te('game.owned') ?><span class="count"><?= fmt_int($libCount) ?></span></a>
    <a href="<?= e(app_url('/wishlist')) ?>" aria-selected="true"><?= te('library.wishlist_title') ?></a>
</nav>

<?php if (!$games): ?>
    <div class="empty">
        <p><?= te('library.wishlist.ban_chua_them_tro_choi') ?></p>
        <a class="btn primary" href="<?= e(app_url('/games')) ?>"><?= te('bundle.index.kham_pha_tro_choi') ?></a>
    </div>
<?php else: ?>
<section class="panel">
    <div class="rows">
        <?php foreach ($games as $g): $gid = (int)$g['ma']; $isOwned = in_array($gid, $owned, true); ?>
            <div class="row lib-row">
                <a class="thumb" href="<?= e(game_url($g)) ?>">
                    <?php if ($g['anh_bia']): ?><img src="<?= e(app_media_url($g['anh_bia'])) ?>" alt="<?= e($g['anh_bia_alt'] ?: $g['tieu_de']) ?>" loading="lazy" data-fallback="<?= e($g['tieu_de']) ?>"><?php else: ?><span class="cover-fallback"><?= e($g['tieu_de']) ?></span><?php endif; ?>
                </a>
                <div class="grow">
                    <a class="link" href="<?= e(game_url($g)) ?>"><?= e($g['tieu_de']) ?></a>
                    <span class="sub"><?= te('library.wishlist.them', [ e($g['nha_phat_trien'] ?: label('release_status', $g['trang_thai_phat_hanh'])) ,  e(time_ago($g['ngay_them'])) ]) ?></span>
                </div>
                <?= price_html($g, $isOwned) ?>
                <div class="cluster">
                    <?php if ($isOwned): ?>
                        <a class="btn sm" href="<?= e(app_url('/library')) ?>"><?= te('library.wishlist.trong_thu_vien') ?></a>
                    <?php elseif (game_is_free($g)): ?>
                        <form method="post" action="<?= e(app_url('/games/' . rawurlencode($g['duong_dan_rut_gon']) . '/claim')) ?>"><?= csrf_field() ?><?= return_field('/wishlist') ?><button class="btn sm primary" type="submit"><?= te('game.claim_free') ?></button></form>
                    <?php elseif (in_array($gid, $inCart, true)): ?>
                        <a class="btn sm" href="<?= e(app_url('/cart')) ?>"><?= te('library.wishlist.da_trong_gio') ?></a>
                    <?php elseif ($g['mo_hinh_gia'] === 'pay_what_you_want'): ?>
                        <a class="btn sm primary" href="<?= e(game_url($g)) ?>"><?= te('library.wishlist.chon_muc_gia') ?></a>
                    <?php else: ?>
                        <form method="post" action="<?= e(app_url('/cart/add')) ?>"><?= csrf_field() ?><?= return_field('/wishlist') ?><input type="hidden" name="type" value="game"><input type="hidden" name="id" value="<?= $gid ?>"><button class="btn sm primary" type="submit"><?= te('game.add_to_cart') ?></button></form>
                    <?php endif; ?>
                    <form method="post" action="<?= e(app_url('/games/' . rawurlencode($g['duong_dan_rut_gon']) . '/wishlist')) ?>"><?= csrf_field() ?><?= return_field('/wishlist') ?><button class="link-btn" type="submit"><?= te('collection.show.bo') ?></button></form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
</section>
<?php endif; ?>
