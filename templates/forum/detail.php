<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section class="forum-detail">
    <p><a href="<?php echo app_url('/forums'); ?>">← Diễn đàn</a></p>
    <h2><?php echo $escape($forum['tieu_de']); ?></h2>
    <?php if (!empty($forum['mo_ta'])): ?><p><?php echo $escape($forum['mo_ta']); ?></p><?php endif; ?>
    <?php if (!empty($_SESSION['user'])): ?>
        <p><a class="button" href="<?php echo app_url('/forums/' . (int)$forum['ma'] . '/topics/new'); ?>">Tạo chủ đề</a></p>
    <?php else: ?>
        <p><a href="<?php echo app_url('/auth/login'); ?>">Đăng nhập</a> để tạo chủ đề.</p>
    <?php endif; ?>
    <?php if (!$topics): ?><p>Chưa có chủ đề thảo luận.</p><?php else: ?>
        <?php foreach ($topics as $topic): ?>
            <article class="card forum-topic">
                <h3><a href="<?php echo app_url('/forums/' . (int)$forum['ma'] . '/topics/' . (int)$topic['ma']); ?>"><?php echo $escape($topic['tieu_de']); ?></a></h3>
                <small>Đăng bởi <strong><?php echo $escape($topic['ten_hien_thi']); ?></strong> · <?php echo $escape($topic['ngay_tao']); ?></small>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<style>
    .forum-topic { margin: 16px 0; }
    .forum-topic > small { color: var(--muted); }
</style>
