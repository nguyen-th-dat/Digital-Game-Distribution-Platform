<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section class="admin-page"><h2>Quản lý trò chơi</h2>
    <nav class="admin-nav" aria-label="Điều hướng quản trị">
        <a href="<?php echo app_url('/admin'); ?>">Tổng quan</a>
        <a href="<?php echo app_url('/admin/users'); ?>">Người dùng</a>
        <a href="<?php echo app_url('/admin/reports'); ?>">Báo cáo</a>
    </nav>
    <form class="admin-search" method="get" action="<?php echo app_url('/admin/games'); ?>">
        <label for="game-search">Tìm theo tên, slug hoặc mã game</label>
        <div><input id="game-search" type="search" name="q" value="<?php echo $escape($query); ?>" placeholder="Nhập tên, slug hoặc mã game">
            <button class="button" type="submit">Tìm game</button></div>
    </form>
    <div class="admin-table-wrap"><table><thead><tr><th>Tiêu đề</th><th>Tác giả ID</th><th>Trạng thái</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead><tbody>
    <?php if (!$games): ?><tr><td colspan="5">Không tìm thấy trò chơi phù hợp.</td></tr><?php endif; ?>
    <?php foreach ($games as $game): ?><tr>
        <td><a href="<?php echo app_url('/games/' . rawurlencode($game['duong_dan_rut_gon'])); ?>"><?php echo $escape($game['tieu_de']); ?></a></td>
        <td><?php echo (int)$game['nguoi_tao_id']; ?></td>
        <td><?php echo $escape($game['trang_thai']); ?></td>
        <td><?php echo $escape($game['ngay_tao']); ?></td>
        <td>
            <?php if ($game['trang_thai'] !== 'hidden'): ?>
                <form method="post" action="<?php echo app_url('/admin/games/' . (int)$game['ma'] . '/remove'); ?>" onsubmit="return confirm('Gỡ trò chơi này khỏi nền tảng? Lịch sử giao dịch sẽ được giữ lại.');">
                    <input type="hidden" name="csrf_token" value="<?php echo $escape($csrfToken); ?>">
                    <button class="button button-danger" type="submit">Gỡ khỏi nền tảng</button>
                </form>
            <?php else: ?>
                <span>Đã gỡ</span>
            <?php endif; ?>
        </td>
    </tr><?php endforeach; ?>
    </tbody></table></div>
    <?php if ($totalPages > 1): ?>
        <nav class="admin-pagination" aria-label="Phân trang trò chơi">
            <?php if ($page > 1): ?><a href="<?php echo app_url('/admin/games') . '?' . htmlspecialchars(http_build_query(['q' => $query, 'page' => $page - 1]), ENT_QUOTES, 'UTF-8'); ?>">← Trước</a><?php endif; ?>
            <span>Trang <?php echo $page; ?> / <?php echo $totalPages; ?></span>
            <?php if ($page < $totalPages): ?><a href="<?php echo app_url('/admin/games') . '?' . htmlspecialchars(http_build_query(['q' => $query, 'page' => $page + 1]), ENT_QUOTES, 'UTF-8'); ?>">Sau →</a><?php endif; ?>
        </nav>
    <?php endif; ?>
</section>
<style>
    .admin-page > h2 { margin-bottom: 12px; }
    .admin-nav { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 22px; }
    .admin-nav a { padding: 8px 13px; border-radius: 12px; background: var(--surface); font-weight: 650; }
    .admin-table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 16px; background: var(--surface); }
    .admin-table-wrap table { min-width: 760px; }
    .admin-table-wrap td form { margin: 0; }
    .admin-search { max-width: 620px; margin: 0 0 20px; }
    .admin-search > div { display: flex; gap: 10px; }
    .admin-search input { width: 100%; }
    .admin-pagination { display: flex; align-items: center; justify-content: center; gap: 18px; margin: 20px 0; }
    .admin-pagination a { padding: 8px 12px; border-radius: 10px; background: var(--surface); font-weight: 650; }
    .admin-pagination span { color: var(--muted); font-size: 14px; }
    .button-danger { min-height: 36px; padding: 7px 12px; background: #a9424a; box-shadow: none; font-size: 13px; }
    .button-danger:hover { background: #84313a; }
    @media (max-width: 480px) { .admin-search > div { align-items: stretch; flex-direction: column; } }
</style>
