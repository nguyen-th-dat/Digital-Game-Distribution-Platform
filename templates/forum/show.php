<?php
/** One board with its topics. @var array $board @var array $topics @var string $q @var int $page @var int $pages @var int $total */
$id = (int)$board['ma'];
$self = '/forums/' . $id;
$user = current_user();
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url('/forums')) ?>"><?= te('forum.forums') ?></a><span><?= e($board['tieu_de']) ?></span></nav>

<header class="object-head">
    <div class="grow">
        <h1><?= e($board['tieu_de']) ?></h1>
        <p class="meta">
            <?php if ($board['loai_dien_dan'] === 'game'): ?><?= te('forum.show.dien-dan-cua') ?> <a class="link" href="<?= e(app_url('/games/' . rawurlencode($board['game_slug']))) ?>"><?= e($board['game_title']) ?></a>
            <?php elseif ($board['loai_dien_dan'] === 'jam'): ?><?= te('forum.show.dien-dan-cua-cuoc-thi') ?> <a class="link" href="<?= e(app_url('/jams/' . rawurlencode($board['jam_slug']))) ?>"><?= e($board['jam_title']) ?></a>
            <?php else: ?><?= te('forum.show.dien-dan-chung-cua-nen-tang') ?><?php endif; ?>
            <?php if (!(int)$board['cong_khai']): ?> <?= te('forum.show.rieng-tu') ?><?php endif; ?>
        </p>
        <div class="stat-line"><span><strong><?= fmt_int((int)$board['so_chu_de']) ?></strong> <?= te('forum.index.chu_de') ?></span><span><strong><?= fmt_int((int)$board['so_bai']) ?></strong> <?= te('forum.index.tra_loi') ?></span></div>
    </div>
    <div class="actions">
        <?php if ($user): ?>
            <a class="btn primary" href="<?= e(app_url($self . '/topics/new')) ?>"><?= te('forum.new_topic') ?></a>
        <?php else: ?>
            <a class="btn" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($self . '/topics/new'))) ?>"><?= te('forum.show.dang_nhap_de_dang_bai') ?></a>
        <?php endif; ?>
    </div>
</header>

<?php if ($board['mo_ta']): ?><div class="prose section"><?= e_multiline($board['mo_ta']) ?></div><?php endif; ?>

<section class="section" aria-labelledby="topics-h">
    <div class="section-head">
        <h2 id="topics-h"><?= te('forum.topics') ?></h2>
        <form class="search-inline" method="get" action="<?= e(app_url($self)) ?>" role="search">
            <label class="sr-only" for="tq"><?= te('forum.show.tim_chu_de') ?></label>
            <input class="input sm" id="tq" type="search" name="q" value="<?= e($q) ?>" maxlength="100" placeholder="<?= te('forum.show.tim_theo_tieu_de') ?>">
            <button class="btn sm" type="submit"><?= te('forum.show.tim') ?></button>
        </form>
    </div>
    <?php if ($topics): ?>
        <div class="panel">
            <div class="rows">
                <?php foreach ($topics as $t): ?>
                    <div class="row topic-row">
                        <img class="avatar" src="<?= e(avatar_url(['ma' => $t['tac_gia_nguoi_dung_id'], 'url_anh_dai_dien' => $t['url_anh_dai_dien']])) ?>" alt="" loading="lazy">
                        <div class="grow">
                            <?php if ((int)$t['da_ghim']): ?><span class="pill"><?= te('forum.show.da_ghim') ?></span> <?php endif; ?>
                            <?php if ((int)$t['da_khoa']): ?><span class="pill"><?= te('forum.locked') ?></span> <?php endif; ?>
                            <?php if ($t['trang_thai'] !== 'published'): ?><span class="pill warn"><?= e(label('content_status', $t['trang_thai'])) ?></span> <?php endif; ?>
                            <a class="link t" href="<?= e(app_url($self . '/topics/' . (int)$t['ma'])) ?>"><?= e($t['tieu_de']) ?></a>
                            <span class="sub"><?= e($t['ten_hien_thi']) ?> · <?= e(time_ago($t['ngay_tao'])) ?></span>
                        </div>
                        <div class="board-stats">
                            <span><strong><?= fmt_int((int)$t['so_tra_loi']) ?></strong> <?= te('forum.index.tra_loi') ?></span>
                            <span class="faint"><?= $t['tra_loi_cuoi'] ? te('game.sort_new') . ' ' . e(time_ago($t['tra_loi_cuoi'])) : te('forum.show.chua_co_tra_loi') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
    <?php elseif ($q !== ''): ?>
        <div class="empty"><p><?= te('forum.show.khong-co-chu-de-nao-khop-voi', [ e($q) ]) ?></p><a class="btn" href="<?= e(app_url($self)) ?>"><?= te('forum.show.xem_tat_ca_chu_de') ?></a></div>
    <?php else: ?>
        <div class="empty"><p><?= te('forum.show.chua_co_chu_de_nao') ?></p></div>
    <?php endif; ?>
</section>
