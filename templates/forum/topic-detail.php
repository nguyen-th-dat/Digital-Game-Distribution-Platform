<?php
$escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$avatarUrl = static function (array $player): string {
    return !empty($player['url_anh_dai_dien'])
        ? (string)$player['url_anh_dai_dien']
        : app_url('/avatars/' . (int)$player['ma']);
};
?>
<section class="topic-detail">
    <p><a href="<?php echo app_url('/forums/' . (int)$forum['ma']); ?>">← <?php echo $escape($forum['tieu_de']); ?></a></p>
    <article class="card topic-original">
        <a class="topic-avatar-link" href="<?php echo app_url('/author/' . (int)$topicAuthor['ma']); ?>" aria-label="Xem hồ sơ của <?php echo $escape($topicAuthor['ten_hien_thi']); ?>">
            <img class="topic-avatar" src="<?php echo $escape($avatarUrl($topicAuthor)); ?>" alt="">
        </a>
        <div class="topic-message">
            <h2><?php echo $escape($topic['tieu_de']); ?></h2>
            <p class="topic-content"><?php echo nl2br($escape($topic['noi_dung_mo_dau'])); ?></p>
            <small>Đăng bởi <strong><a href="<?php echo app_url('/author/' . (int)$topicAuthor['ma']); ?>"><?php echo $escape($topicAuthor['ten_hien_thi']); ?></a></strong> · <?php echo $escape($topic['ngay_tao']); ?></small>
        </div>
    </article>

    <section class="topic-replies">
        <h3>Bình luận (<?php echo count($posts); ?>)</h3>
        <?php if (!$posts): ?>
            <p>Chưa có bình luận.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="card topic-reply">
                    <a class="topic-avatar-link" href="<?php echo app_url('/author/' . (int)$post['tac_gia_nguoi_dung_id']); ?>" aria-label="Xem hồ sơ của <?php echo $escape($post['ten_hien_thi']); ?>">
                        <img class="topic-avatar" src="<?php echo $escape($avatarUrl([
                            'ma' => $post['tac_gia_nguoi_dung_id'],
                            'url_anh_dai_dien' => $post['url_anh_dai_dien'] ?? null,
                        ])); ?>" alt="">
                    </a>
                    <div class="topic-message">
                        <p><?php echo nl2br($escape($post['noi_dung'])); ?></p>
                        <small><a href="<?php echo app_url('/author/' . (int)$post['tac_gia_nguoi_dung_id']); ?>"><?php echo $escape($post['ten_hien_thi']); ?></a> · <?php echo $escape($post['ngay_tao']); ?></small>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <?php if (!empty($_SESSION['user'])): ?>
        <?php if (empty($topic['da_khoa'])): ?>
            <form class="card reply-form" method="post" action="<?php echo app_url('/forums/' . (int)$forum['ma'] . '/topics/' . (int)$topic['ma'] . '/posts'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $escape($csrfToken); ?>">
                <label for="reply-content">Viết bình luận</label>
                <textarea id="reply-content" name="noi_dung" maxlength="10000" rows="4" required style="width:100%"></textarea>
                <p><button class="button" type="submit">Gửi bình luận</button></p>
            </form>
        <?php else: ?>
            <p>Chủ đề này đã khóa bình luận.</p>
        <?php endif; ?>
    <?php else: ?>
        <p><a href="<?php echo app_url('/auth/login'); ?>">Đăng nhập</a> để bình luận.</p>
    <?php endif; ?>
</section>
<style>
    .topic-content, .topic-reply p { white-space: pre-wrap; }
    .topic-original, .topic-reply { display: flex; align-items: flex-start; gap: 14px; }
    .topic-original { margin-top: 20px; }
    .topic-avatar-link { display: block; flex: 0 0 48px; border-radius: 50%; }
    .topic-avatar { display: block; width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
    .topic-message { min-width: 0; flex: 1; }
    .topic-message h2 { margin-top: 0; }
    .topic-message > p { margin-top: 0; }
    .topic-message small { color: var(--muted); }
    .topic-replies { margin: 28px 0; }
    .topic-reply { margin: 12px 0; }
    .reply-form { margin-top: 22px; }
</style>
