<?php
/** One devlog post. @var array $game @var array $post @var array $comments @var array $replies
 *  @var int $commentCount @var bool $commentsOn @var array $neighbours */
$base = '/games/' . rawurlencode($game['duong_dan_rut_gon']);
$self = $base . '/devlogs/' . rawurlencode($post['duong_dan_rut_gon']);
$user = current_user();
$isAuthor = $user && GameModel::isAuthor((int)$game['ma'], (int)$user['ma']);
$render = function (array $c, bool $reply) use ($user, $self, $commentsOn, $isAuthor) {
    $mine = $user && (int)$user['ma'] === (int)$c['nguoi_dung_id'];
    ?>
    <article class="comment<?= $reply ? ' reply' : '' ?>" id="dcomment-<?= (int)$c['ma'] ?>">
        <img class="avatar" src="<?= e(avatar_url($c)) ?>" alt="" loading="lazy">
        <div>
            <div class="meta"><a class="link" href="<?= e(profile_url($c)) ?>"><?= e($c['ten_hien_thi']) ?></a> · <?= e(time_ago($c['ngay_tao'])) ?></div>
            <div class="prose"><?= e_multiline($c['noi_dung']) ?></div>
            <div class="cluster small faint">
                <?php if (!$reply && $user && $commentsOn): ?>
                    <details class="edit">
                        <summary class="link-btn"><?= te('forum.reply') ?></summary>
                        <form method="post" action="<?= e(app_url($self . '/comments')) ?>" class="form reply-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="parent_id" value="<?= (int)$c['ma'] ?>">
                            <label class="sr-only" for="dreply-<?= (int)$c['ma'] ?>"><?= te('devlog.show.noi_dung_tra_loi') ?></label>
                            <textarea class="input" id="dreply-<?= (int)$c['ma'] ?>" name="noi_dung" maxlength="5000" required></textarea>
                            <div class="form-actions"><button class="btn sm primary" type="submit"><?= te('devlog.show.gui_tra_loi') ?></button></div>
                        </form>
                    </details>
                <?php endif; ?>
                <?php if ($mine || $isAuthor || is_staff()): ?>
                    <form method="post" action="<?= e(app_url('/devlog-comments/' . (int)$c['ma'] . '/delete')) ?>" data-confirm="<?= te('devlog.show.xoa-binh-luan-nay') ?>">
                        <?= csrf_field() ?><?= return_field() ?>
                        <button class="link-btn neg" type="submit"><?= te('admin.delete') ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php
};
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url($base)) ?>"><?= e($game['tieu_de']) ?></a><a href="<?= e(app_url($base . '/devlogs')) ?>"><?= te('devlog.title') ?></a><span><?= e($post['tieu_de']) ?></span></nav>

<article class="devlog">
    <header class="devlog-head">
        <h1><?= e($post['tieu_de']) ?></h1>
        <p class="meta"><img class="avatar sm" src="<?= e(avatar_url(['ma' => $post['tac_gia_nguoi_dung_id'], 'url_anh_dai_dien' => $post['url_anh_dai_dien']])) ?>" alt="">
            <a class="link" href="<?= e(profile_url($post)) ?>"><?= e($post['ten_hien_thi']) ?></a> · <?= e(fmt_datetime($post['ngay_cong_bo'])) ?></p>
    </header>
    <div class="prose devlog-body"><?= e_multiline($post['noi_dung']) ?></div>
    <footer class="cluster small faint">
        <?php if ($isAuthor): ?><a class="link" href="<?= e(app_url('/dev/games/' . (int)$game['ma'] . '/devlogs/' . (int)$post['ma'])) ?>"><?= te('devlog.show.sua_bai') ?></a><?php endif; ?>
        <?php if (!$isAuthor) partial('report', ['type' => 'devlog', 'id' => (int)$post['ma'], 'labelText' => te('devlog.show.bao_cao_bai_viet')]); ?>
    </footer>
</article>

<?php if ($neighbours): ?>
    <nav class="post-nav" aria-label="<?= te('devlog.show.bai_khac') ?>">
        <?php if (isset($neighbours['older'])): ?><a class="row-link" href="<?= e(app_url($base . '/devlogs/' . rawurlencode($neighbours['older']['duong_dan_rut_gon']))) ?>"><span class="small faint"><?= te('devlog.show.bai_truoc') ?></span><strong><?= e($neighbours['older']['tieu_de']) ?></strong></a><?php else: ?><span></span><?php endif; ?>
        <?php if (isset($neighbours['newer'])): ?><a class="row-link next" href="<?= e(app_url($base . '/devlogs/' . rawurlencode($neighbours['newer']['duong_dan_rut_gon']))) ?>"><span class="small faint"><?= te('devlog.show.bai_sau') ?></span><strong><?= e($neighbours['newer']['tieu_de']) ?></strong></a><?php endif; ?>
    </nav>
<?php endif; ?>

<section class="section devlog-comments" id="comments" aria-labelledby="dc-h">
    <div class="section-head"><h2 id="dc-h"><?= te('devlog.comments') ?></h2><span class="meta"><?= te('devlog.show.binh-luan', [ fmt_int($commentCount) ]) ?></span></div>
    <?php if (!$commentsOn): ?>
        <p class="muted"><?= te('devlog.show.binh_luan_da_bi_tat') ?></p>
    <?php elseif ($user): ?>
        <form method="post" action="<?= e(app_url($self . '/comments')) ?>" class="form comment-form">
            <?= csrf_field() ?>
            <label class="sr-only" for="dcomment-body"><?= te('devlog.show.binh_luan_cua_ban') ?></label>
            <textarea class="input" id="dcomment-body" name="noi_dung" maxlength="5000" required placeholder="<?= te('devlog.show.gop_y_cho_ban_cap') ?>"></textarea>
            <div class="form-actions"><button class="btn primary" type="submit"><?= te('game.post_comment') ?></button></div>
        </form>
    <?php else: ?>
        <p class="muted"><a class="link" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($self . '#comments'))) ?>"><?= te('auth.login_button') ?></a> <?= te('devlog.show.de_binh_luan') ?></p>
    <?php endif; ?>
    <?php if ($comments): ?>
        <div class="comments">
            <?php foreach ($comments as $c): ?>
                <?php $render($c, false); ?>
                <?php foreach ($replies[(int)$c['ma']] ?? [] as $r) $render($r, true); ?>
            <?php endforeach; ?>
        </div>
    <?php elseif ($commentsOn): ?>
        <div class="empty"><?= te('game.no_comments') ?></div>
    <?php endif; ?>
</section>
