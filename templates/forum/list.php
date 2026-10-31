<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section>
    <div class="forum-heading">
        <h2>Diễn đàn</h2>
        <a class="button" href="<?php echo app_url('/forums/new'); ?>">Tạo diễn đàn</a>
    </div>
    <?php if (!$forumGroups): ?><p>Chưa có diễn đàn nào.</p><?php else: ?>
        <?php foreach ($forumGroups as $group): ?>
            <section class="forum-date-group">
                <h3>Ngày <?php echo $escape($group['date']); ?></h3>
                <div class="grid"><?php foreach ($group['forums'] as $forum): ?><article class="card">
                    <h4><a href="<?php echo app_url('/forums/' . (int)$forum['ma']); ?>"><?php echo $escape($forum['tieu_de']); ?></a></h4>
                    <p><?php echo $escape($forum['mo_ta'] ?? ''); ?></p>
                    <?php $createdAt = strtotime((string)($forum['ngay_tao'] ?? '')); ?>
                    <?php if ($createdAt !== false): ?><small>Được tạo lúc <?php echo date('H:i', $createdAt); ?></small><?php endif; ?>
                </article><?php endforeach; ?></div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<style>
    .forum-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
    .forum-heading h2 { margin: 0; }
    .forum-date-group { margin: 24px 0 32px; }
    .forum-date-group > h3 { padding-bottom: 10px; border-bottom: 1px solid var(--line); }
    .forum-date-group .card h4 { margin: 0 0 8px; }
    .forum-date-group .card p { margin: 0 0 8px; }
    .forum-date-group .card small { color: var(--muted); }
    @media (max-width: 480px) {
        .forum-heading { align-items: flex-start; flex-direction: column; }
    }
</style>
