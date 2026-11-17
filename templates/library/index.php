<?php
/** Owned games. @var array $games @var string $q @var string $sort @var int $wishCount */
?>
<div class="title-row">
    <div>
        <h1><?= te('library.title') ?></h1>
        <p class="sub"><?= te('library.index.tro-choi', [ fmt_int($total) ,  $q !== '' ? ' khớp "' . e($q) . '"' : '' ]) ?></p>
    </div>
</div>

<nav class="tabs" aria-label="<?= te('library.title') ?>">
    <a href="<?= e(app_url('/library')) ?>" aria-selected="true"><?= te('game.owned') ?></a>
    <a href="<?= e(app_url('/wishlist')) ?>" aria-selected="false"><?= te('library.wishlist_title') ?><span class="count"><?= fmt_int($wishCount) ?></span></a>
</nav>

<div class="grid">
    <section class="panel span-8" aria-label="<?= te('library.index.tro_choi_da_so_huu') ?>">
        <form class="toolbar" method="get" action="<?= e(app_url('/library')) ?>" role="search">
            <label class="sr-only" for="lib-q"><?= te('library.index.tim_trong_thu_vien') ?></label>
            <input class="input search" id="lib-q" type="search" name="q" value="<?= e($q) ?>" placeholder="<?= te('library.index.tim_trong_thu_vien') ?>" maxlength="100">
            <label class="sr-only" for="lib-sort"><?= te('common.sort') ?></label>
            <select class="input" id="lib-sort" name="sort">
                <option value="recent"<?= $sort === 'recent' ? ' selected' : '' ?>><?= te('library.index.moi_nhan_gan_day') ?></option>
                <option value="title"<?= $sort === 'title' ? ' selected' : '' ?>><?= te('library.index.ten_a_z') ?></option>
            </select>
            <button class="btn" type="submit"><?= te('common.filter') ?></button>
        </form>

        <?php if (!$games): ?>
            <div class="empty">
                <p><?= $q !== '' ? te('library.index.khong.co.tro.choi.nao.khop') : te('library.index.thu.vien.con.trong') ?></p>
                <a class="btn primary" href="<?= e(app_url('/games')) ?>"><?= te('bundle.index.kham_pha_tro_choi') ?></a>
            </div>
        <?php else: ?>
            <div class="rows">
                <?php foreach ($games as $g): ?>
                    <div class="row lib-row">
                        <a class="thumb" href="<?= e(game_url($g)) ?>">
                            <?php if ($g['anh_bia']): ?><img src="<?= e(app_media_url($g['anh_bia'])) ?>" alt="<?= e($g['anh_bia_alt'] ?: $g['tieu_de']) ?>" loading="lazy" data-fallback="<?= e($g['tieu_de']) ?>"><?php else: ?><span class="cover-fallback"><?= e($g['tieu_de']) ?></span><?php endif; ?>
                        </a>
                        <div class="grow">
                            <a class="link" href="<?= e(game_url($g)) ?>"><?= e($g['tieu_de']) ?></a>
                            <span class="sub"><?= e(label('ownership', $g['loai_nhan_quyen'])) ?> · <?= e(fmt_date($g['ngay_nhan_quyen'])) ?><?= $g['phien_ban_moi'] ? ' ' . te('library.index.dot_ban_moi_nhat') . ' ' . e($g['phien_ban_moi']) : '' ?></span>
                        </div>
                        <?php $playUrl = game_play_url($g); ?>
                        <div class="cluster">
                            <?php if ($playUrl): ?>
                                <a class="btn sm primary" href="<?= e($playUrl) ?>" target="_blank" rel="noopener noreferrer"><?= te('game.play') ?></a>
                            <?php endif; ?>
                            <?php if ((int)$g['so_lien_tai'] > 0): ?>
                                <a class="btn sm<?= $playUrl ? '' : ' primary' ?>" href="<?= e(app_url('/games/' . rawurlencode($g['duong_dan_rut_gon']) . '/downloads')) ?>"><?= te('library.index.tai-ve', [ (int)$g['so_lien_tai'] > 1 ? ' ' . te('common.dot') . ' ' . fmt_int((int)$g['so_lien_tai']) : '' ]) ?></a>
                            <?php elseif (!$playUrl): ?>
                                <a class="btn sm" href="<?= e(game_url($g)) ?>#releases"><?= te('library.index.xem_ban_phat_hanh') ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
        <?php endif; ?>
    </section>

    <aside class="span-4">
        <section class="panel" aria-labelledby="redeem-h">
            <header><h2 id="redeem-h"><?= te('library.index.kich_hoat_ma') ?></h2></header>
            <form class="form" method="post" action="<?= e(app_url('/keys/redeem')) ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label for="key"><?= te('library.index.ma_kich_hoat') ?></label>
                    <input class="input" id="key" name="key" required maxlength="80" autocomplete="off" spellcheck="false" placeholder="XXXX-XXXX-XXXX-XXXX">
                    <span class="hint"><?= te('library.index.nha_phat_trien_phat_ma') ?></span>
                </div>
                <div class="form-actions"><button class="btn primary" type="submit"><?= te('admin.activate') ?></button></div>
            </form>
        </section>
    </aside>
</div>
