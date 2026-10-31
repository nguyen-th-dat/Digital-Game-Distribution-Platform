<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section class="notifications-page">
    <p><a href="<?php echo app_url('/account'); ?>">← Tài khoản</a></p>
    <h2>Thông báo</h2>
    <?php if (!$notifications): ?>
        <div class="card notification-empty"><h3>Bạn chưa có thông báo</h3><p>Khi game hoặc người chơi bạn theo dõi có cập nhật, thông báo sẽ xuất hiện ở đây.</p></div>
    <?php else: ?>
        <?php foreach ($notifications as $notification): ?>
            <article class="card notification-item <?php echo empty($notification['da_doc']) ? 'notification-unread' : ''; ?>">
                <div class="notification-copy">
                    <h3><?php echo $escape($notification['tieu_de']); ?></h3>
                    <?php if (!empty($notification['noi_dung_thong_bao'])): ?><p><?php echo $escape($notification['noi_dung_thong_bao']); ?></p><?php endif; ?>
                    <small><?php echo $escape($notification['ngay_tao']); ?> · <?php echo empty($notification['da_doc']) ? 'Chưa đọc' : 'Đã đọc'; ?></small>
                </div>
                <div class="notification-actions">
                    <?php if (!empty($notification['url_dich'])): ?>
                        <a class="button notification-open" href="<?php echo app_url($notification['url_dich']); ?>">Xem cập nhật</a>
                    <?php endif; ?>
                    <?php if (empty($notification['da_doc'])): ?>
                        <form method="post" action="<?php echo app_url('/notifications/' . (int)$notification['ma'] . '/read'); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $escape($csrfToken); ?>">
                            <button class="notification-mark-read" type="submit">Đánh dấu đã đọc</button>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<style>
    .notification-item { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin: 14px 0; }
    .notification-unread { border-color: rgba(112, 71, 235, .35); background: #f0ebff; }
    .notification-copy { min-width: 0; }
    .notification-copy h3 { margin: 0 0 5px; font-size: 18px; }
    .notification-copy p { margin: 0 0 6px; }
    .notification-copy small { color: var(--muted); }
    .notification-actions { display: flex; flex: 0 0 auto; align-items: center; gap: 12px; }
    .notification-open { min-height: 38px; padding: 7px 12px; font-size: 13px; }
    .notification-mark-read { padding: 6px 0; border: 0; color: var(--purple-dark); background: transparent; font: inherit; font-size: 13px; cursor: pointer; }
    .notification-empty { max-width: 620px; }
    @media (max-width: 640px) {
        .notification-item { align-items: flex-start; flex-direction: column; }
        .notification-actions { align-items: flex-start; flex-direction: column; gap: 5px; }
    }
</style>
