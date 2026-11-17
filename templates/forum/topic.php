<?php
/**
 * Topic with paginated replies.
 * @var array $board @var array $topic @var array $posts @var array $reactionCounts @var array $myReactions
 * @var bool $canManage @var int $page @var int $pages @var int $total
 */
$user = current_user();
$uid = current_user_id();
$staff = is_staff();
$boardUrl = '/forums/' . (int)$board['ma'];
$self = $boardUrl . '/topics/' . (int)$topic['ma'];
$isAuthor = $uid && (int)$topic['tac_gia_nguoi_dung_id'] === $uid;
$locked = (bool)(int)$topic['da_khoa'];
$open = $topic['trang_thai'] === 'published' && (!$locked || $canManage);
$reactionLabels = ['like' => te('label.reaction.like'), 'helpful' => te('label.reaction.helpful'), 'love' => te('label.reaction.love'), 'laugh' => 'Vui'];
$here = $self . ($page > 1 ? '?page=' . $page : '');
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url('/forums')) ?>"><?= te('forum.forums') ?></a><a href="<?= e(app_url($boardUrl)) ?>"><?= e($board['tieu_de']) ?></a><span><?= e($topic['tieu_de']) ?></span></nav>

<div class="title-row">
    <div>
        <h1><?= e($topic['tieu_de']) ?></h1>
        <p class="sub">
            <?php if ((int)$topic['da_ghim']): ?><span class="pill"><?= te('forum.show.da_ghim') ?></span> <?php endif; ?>
            <?php if ($locked): ?><span class="pill"><?= te('forum.locked') ?></span> <?php endif; ?>
            <?php if ($topic['trang_thai'] !== 'published'): ?><span class="pill warn"><?= e(label('content_status', $topic['trang_thai'])) ?></span> <?php endif; ?>
            <?= te('forum.topic.tra-loi', [ fmt_int($total) ]) ?>
        </p>
    </div>
</div>

<div class="topic-layout">
<div class="topic-main">
    <article class="panel post opening" id="topic">
        <img class="avatar" src="<?= e(avatar_url(['ma' => $topic['tac_gia_nguoi_dung_id'], 'url_anh_dai_dien' => $topic['url_anh_dai_dien']])) ?>" alt="">
        <div>
            <div class="meta"><a class="link" href="<?= e(profile_url($topic)) ?>"><?= e($topic['ten_hien_thi']) ?></a> · <?= e(fmt_datetime($topic['ngay_tao'])) ?></div>
            <div class="prose"><?= e_multiline($topic['noi_dung_mo_dau']) ?></div>
            <div class="cluster small faint post-actions">
                <?php if ($isAuthor): ?>
                    <details class="edit" id="edit-topic">
                        <summary class="link-btn"><?= te('common.edit') ?></summary>
                        <form class="form well" method="post" action="<?= e(app_url($self . '/moderate')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="edit">
                            <div class="field"><label for="et-title"><?= te('common.title') ?></label><input class="input" id="et-title" name="tieu_de" minlength="3" maxlength="220" required value="<?= e($topic['tieu_de']) ?>"></div>
                            <div class="field"><label for="et-body"><?= te('admin.comments') ?></label><textarea class="input tall" id="et-body" name="noi_dung" maxlength="20000" required><?= e($topic['noi_dung_mo_dau']) ?></textarea></div>
                            <div class="form-actions"><button class="btn sm primary" type="submit"><?= te('common.save') ?></button></div>
                        </form>
                    </details>
                <?php endif; ?>
                <?php if ($isAuthor || $canManage): ?>
                    <form method="post" action="<?= e(app_url($self . '/moderate')) ?>" data-confirm="<?= te('forum.topic.go-chu-de-nay-cung-toan-bo-tra-loi-khoi-dien-dan') ?>">
                        <?= csrf_field() ?><input type="hidden" name="action" value="remove">
                        <button class="link-btn neg" type="submit"><?= te('forum.topic.go_chu_de') ?></button>
                    </form>
                <?php endif; ?>
                <?php if (!$isAuthor) partial('report', ['type' => 'community_topic', 'id' => (int)$topic['ma']]); ?>
            </div>
        </div>
    </article>

    <section class="panel section" aria-labelledby="replies-h">
        <header><h2 id="replies-h"><?= te('forum.reply') ?></h2><span class="meta"><?= fmt_int($total) ?></span></header>
        <?php if (!$posts): ?>
            <p class="muted"><?= te('forum.topic.chua_co_tra_loi_nao') ?></p>
        <?php endif; ?>
        <?php foreach ($posts as $p):
            $pid = (int)$p['ma'];
            $mine = $uid && (int)$p['tac_gia_nguoi_dung_id'] === $uid;
            $counts = $reactionCounts[$pid] ?? [];
            ?>
            <article class="post" id="post-<?= $pid ?>">
                <img class="avatar" src="<?= e(avatar_url(['ma' => $p['tac_gia_nguoi_dung_id'], 'url_anh_dai_dien' => $p['url_anh_dai_dien']])) ?>" alt="" loading="lazy">
                <div>
                    <div class="meta">
                        <a class="link" href="<?= e(profile_url($p)) ?>"><?= e($p['ten_hien_thi']) ?></a> <?= te('admin.payouts.text') ?> <a href="#post-<?= $pid ?>"><?= e(time_ago($p['ngay_tao'])) ?></a>
                        <?php if ($p['ngay_chinh_sua']): ?> <?= te('forum.topic.da-sua') ?><?php endif; ?>
                        <?php if ($p['bai_viet_cha_id']): ?> <?= te('forum.topic.tra-loi.2') ?> <a class="link" href="#post-<?= (int)$p['bai_viet_cha_id'] ?>"><?= $p['cha_trang_thai'] === 'published' ? e($p['cha_ten']) : te('forum.topic.bai_da_go') ?></a><?php endif; ?>
                        <?php if ($p['trang_thai'] !== 'published'): ?> <span class="pill warn"><?= e(label('content_status', $p['trang_thai'])) ?></span><?php endif; ?>
                    </div>
                    <div class="prose"><?= e_multiline($p['noi_dung']) ?></div>
                    <div class="reactions">
                        <?php foreach (ForumModel::REACTIONS as $r):
                            $n = $counts[$r] ?? 0;
                            $pressed = !empty($myReactions[$pid][$r]);
                            if ($user && !$mine): ?>
                                <form method="post" action="<?= e(app_url('/posts/' . $pid . '/react')) ?>">
                                    <?= csrf_field() ?><?= return_field($here . '#post-' . $pid) ?>
                                    <button class="react" type="submit" name="reaction" value="<?= e($r) ?>" aria-pressed="<?= $pressed ? 'true' : 'false' ?>"><?= e($reactionLabels[$r]) ?><?= $n ? ' ' . fmt_int($n) : '' ?></button>
                                </form>
                            <?php elseif ($n): ?>
                                <span class="react" aria-label="<?= e($reactionLabels[$r]) ?>"><?= e($reactionLabels[$r]) ?> <?= fmt_int($n) ?></span>
                            <?php endif;
                        endforeach; ?>
                    </div>
                    <div class="cluster small faint post-actions">
                        <?php if ($user && $open && $p['trang_thai'] === 'published'): ?>
                            <details class="edit">
                                <summary class="link-btn"><?= te('forum.reply') ?></summary>
                                <form method="post" action="<?= e(app_url($self . '/posts')) ?>" class="form reply-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="parent_id" value="<?= $pid ?>">
                                    <label class="sr-only" for="r-<?= $pid ?>"><?= te('forum.topic.tra-loi.3', [ e($p['ten_hien_thi']) ]) ?></label>
                                    <textarea class="input" id="r-<?= $pid ?>" name="noi_dung" maxlength="20000" required></textarea>
                                    <div class="form-actions"><button class="btn sm primary" type="submit"><?= te('devlog.show.gui_tra_loi') ?></button></div>
                                </form>
                            </details>
                        <?php endif; ?>
                        <?php if ($mine && (!$locked || $staff)): ?>
                            <details class="edit">
                                <summary class="link-btn"><?= te('common.edit') ?></summary>
                                <form method="post" action="<?= e(app_url('/posts/' . $pid . '/edit')) ?>" class="form reply-form">
                                    <?= csrf_field() ?><?= return_field($here . '#post-' . $pid) ?>
                                    <label class="sr-only" for="e-<?= $pid ?>"><?= te('forum.edit') ?></label>
                                    <textarea class="input" id="e-<?= $pid ?>" name="noi_dung" maxlength="20000" required><?= e($p['noi_dung']) ?></textarea>
                                    <div class="form-actions"><button class="btn sm primary" type="submit"><?= te('common.save') ?></button></div>
                                </form>
                            </details>
                        <?php endif; ?>
                        <?php if ($mine || $canManage): ?>
                            <form method="post" action="<?= e(app_url('/posts/' . $pid . '/delete')) ?>" data-confirm="<?= te('forum.topic.xoa-bai-viet-nay') ?>">
                                <?= csrf_field() ?><?= return_field($here) ?>
                                <button class="link-btn neg" type="submit"><?= te('admin.delete') ?></button>
                            </form>
                        <?php endif; ?>
                        <?php if (!$mine) partial('report', ['type' => 'community_post', 'id' => $pid]); ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
        <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
    </section>

    <section class="panel section" id="reply" aria-labelledby="reply-h">
        <header><h2 id="reply-h"><?= te('forum.topic.viet_tra_loi') ?></h2></header>
        <?php if (!$user): ?>
            <p class="muted"><?= te('forum.topic.dang_nhap_de_tham_gia') ?></p>
            <a class="btn" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($self))) ?>"><?= te('auth.login_button') ?></a>
        <?php elseif ($topic['trang_thai'] !== 'published'): ?>
            <p class="muted"><?= te('forum.topic.chu_de_dang_bi_an') ?></p>
        <?php elseif ($locked && !$canManage): ?>
            <p class="muted"><?= te('forum.topic.chu_de_da_khoa_khong') ?></p>
        <?php else: ?>
            <form class="form" method="post" action="<?= e(app_url($self . '/posts')) ?>">
                <?= csrf_field() ?>
                <label class="sr-only" for="reply-body"><?= te('devlog.show.noi_dung_tra_loi') ?></label>
                <textarea class="input tall" id="reply-body" name="noi_dung" maxlength="20000" required><?= e(old('noi_dung')) ?></textarea>
                <div class="form-actions"><button class="btn primary" type="submit"><?= te('devlog.show.gui_tra_loi') ?></button><?php if ($locked): ?><span class="small faint"><?= te('forum.topic.chu_de_da_khoa_ban') ?></span><?php endif; ?></div>
            </form>
        <?php endif; ?>
    </section>
</div>

<aside class="topic-side">
    <section class="panel">
        <header><h2><?= te('forum.forums') ?></h2></header>
        <p><a class="link" href="<?= e(app_url($boardUrl)) ?>"><?= e($board['tieu_de']) ?></a></p>
        <?php if ($board['loai_dien_dan'] === 'game'): ?><p class="small muted"><?= te('forum.topic.ve_tro_choi') ?> <a class="link" href="<?= e(app_url('/games/' . rawurlencode($board['game_slug']))) ?>"><?= e($board['game_title']) ?></a></p><?php endif; ?>
        <p class="small faint"><?= te('forum.topic.mo-luc', [ e(fmt_datetime($topic['ngay_tao'])) ]) ?></p>
    </section>
    <?php if ($canManage): ?>
    <section class="panel">
        <header><h2><?= te('forum.topic.quan_ly_chu_de') ?></h2></header>
        <div class="cluster">
            <?php foreach ([(int)$topic['da_ghim'] ? 'unpin' : 'pin', $locked ? 'unlock' : 'lock'] as $a): ?>
                <form method="post" action="<?= e(app_url($self . '/moderate')) ?>">
                    <?= csrf_field() ?><input type="hidden" name="action" value="<?= e($a) ?>">
                    <button class="btn sm" type="submit"><?= e(['pin' => 'Ghim', 'unpin' => te('forum.topic.bo_ghim'), 'lock' => te('common.key'), 'unlock' => te('forum.topic.mo_khoa')][$a]) ?></button>
                </form>
            <?php endforeach; ?>
        </div>
        <?php if ($staff): ?>
            <form class="form mod-form" method="post" action="<?= e(app_url($self . '/moderate')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="<?= $topic['trang_thai'] === 'hidden' ? 'restore' : 'hide' ?>">
                <div class="field"><label for="mod-reason"><?= te('forum.topic.ly_do_ghi_vao_nhat') ?></label><input class="input sm" id="mod-reason" name="ly_do" maxlength="1000"></div>
                <button class="btn sm" type="submit"><?= $topic['trang_thai'] === 'hidden' ? te('forum.topic.hien_lai_chu_de') : te('forum.topic.an_chu_de') ?></button>
            </form>
        <?php endif; ?>
    </section>
    <?php endif; ?>
</aside>
</div>
