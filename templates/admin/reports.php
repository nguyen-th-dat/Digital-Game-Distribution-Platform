<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section>
    <h2>Báo cáo nội dung đang mở</h2>
    <nav class="admin-nav" aria-label="Điều hướng quản trị">
        <a href="<?php echo app_url('/admin'); ?>">Tổng quan</a>
        <a href="<?php echo app_url('/admin/games'); ?>">Trò chơi</a>
        <a href="<?php echo app_url('/admin/users'); ?>">Người dùng và vai trò</a>
    </nav>
    <?php if (!$reports): ?><p>Không có báo cáo đang chờ.</p><?php else: ?>
        <?php foreach ($reports as $report): ?><article class="card" style="margin:12px 0">
            <strong><?php echo $escape($report['loai_doi_tuong']); ?> #<?php echo (int)$report['doi_tuong_id']; ?></strong>
            <p>Lý do: <?php echo $escape($report['ma_ly_do']); ?></p>
            <p><?php echo nl2br($escape($report['chi_tiet'] ?? '')); ?></p>
            <small>Báo cáo bởi: <?php echo $escape($report['reporter_name'] ?? 'Ẩn danh'); ?> · <?php echo $escape($report['ngay_tao']); ?></small>
        </article><?php endforeach; ?>
    <?php endif; ?>
</section>
<style>
    .admin-nav { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 22px; }
    .admin-nav a { padding: 8px 13px; border-radius: 12px; background: var(--surface); font-weight: 650; }
</style>
