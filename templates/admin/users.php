<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section class="admin-page"><h2>Người dùng và vai trò</h2>
    <nav class="admin-nav" aria-label="Điều hướng quản trị">
        <a href="<?php echo app_url('/admin'); ?>">Tổng quan</a>
        <a href="<?php echo app_url('/admin/games'); ?>">Trò chơi</a>
        <a href="<?php echo app_url('/admin/reports'); ?>">Báo cáo</a>
    </nav>
    <p>Chọn các vai trò cần gán cho tài khoản. Vai trò quản trị viên chỉ nên cấp cho người đáng tin cậy.</p>
    <form class="admin-search" method="get" action="<?php echo app_url('/admin/users'); ?>">
        <label for="user-search">Tìm theo tên, tên đăng nhập, email hoặc mã tài khoản</label>
        <div><input id="user-search" type="search" name="q" value="<?php echo $escape($query); ?>" placeholder="Nhập thông tin tài khoản">
            <button class="button" type="submit">Tìm người dùng</button></div>
    </form>
    <div class="admin-table-wrap"><table><thead><tr><th>Tài khoản</th><th>Email</th><th>Trạng thái</th><th>Ngày tạo</th><th>Vai trò</th></tr></thead><tbody>
    <?php if (!$users): ?><tr><td colspan="5">Không tìm thấy tài khoản phù hợp.</td></tr><?php endif; ?>
    <?php foreach ($users as $account): ?><tr>
        <td><strong><?php echo $escape($account['ten_hien_thi']); ?></strong><br><small>@<?php echo $escape($account['ten_dang_nhap']); ?></small></td>
        <td><?php echo $escape($account['email']); ?></td>
        <td><?php echo $escape($account['trang_thai']); ?></td>
        <td><?php echo $escape($account['ngay_tao']); ?></td>
        <td>
            <?php if ((int)$account['ma'] === (int)$user['ma']): ?>
                <span>Không thể tự thay đổi</span>
            <?php else: ?>
                <form class="role-form" method="post" action="<?php echo app_url('/admin/users/' . (int)$account['ma'] . '/roles'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $escape($csrfToken); ?>">
                    <div class="role-options">
                        <?php foreach ($roles as $role): ?>
                            <label>
                                <input type="checkbox" name="role_ids[]" value="<?php echo (int)$role['ma']; ?>" <?php echo in_array($role['ma_vai_tro'], $userRoles[(int)$account['ma']] ?? [], true) ? 'checked' : ''; ?>>
                                <?php echo $escape($role['ten_vai_tro']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button class="button role-save" type="submit">Lưu vai trò</button>
                </form>
            <?php endif; ?>
        </td>
    </tr><?php endforeach; ?>
    </tbody></table></div>
    <?php if ($totalPages > 1): ?>
        <nav class="admin-pagination" aria-label="Phân trang người dùng">
            <?php if ($page > 1): ?><a href="<?php echo app_url('/admin/users') . '?' . htmlspecialchars(http_build_query(['q' => $query, 'page' => $page - 1]), ENT_QUOTES, 'UTF-8'); ?>">← Trước</a><?php endif; ?>
            <span>Trang <?php echo $page; ?> / <?php echo $totalPages; ?></span>
            <?php if ($page < $totalPages): ?><a href="<?php echo app_url('/admin/users') . '?' . htmlspecialchars(http_build_query(['q' => $query, 'page' => $page + 1]), ENT_QUOTES, 'UTF-8'); ?>">Sau →</a><?php endif; ?>
        </nav>
    <?php endif; ?>
</section>
<style>
    .admin-page > h2 { margin-bottom: 12px; }
    .admin-nav { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 18px; }
    .admin-nav a { padding: 8px 13px; border-radius: 12px; background: var(--surface); font-weight: 650; }
    .admin-search { max-width: 720px; margin: 0 0 20px; }
    .admin-search > div { display: flex; gap: 10px; }
    .admin-search input { width: 100%; }
    .admin-table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 16px; background: var(--surface); }
    .admin-table-wrap table { min-width: 920px; }
    .role-form { min-width: 230px; }
    .role-options { display: grid; gap: 5px; margin-bottom: 10px; }
    .role-options label { display: flex; align-items: center; gap: 8px; margin: 0; font-size: 13px; }
    .role-options input { width: 16px; min-height: 16px !important; height: 16px; margin: 0; accent-color: var(--purple); }
    .role-save { min-height: 36px; padding: 7px 12px; font-size: 13px; }
    .admin-pagination { display: flex; align-items: center; justify-content: center; gap: 18px; margin: 20px 0; }
    .admin-pagination a { padding: 8px 12px; border-radius: 10px; background: var(--surface); font-weight: 650; }
    .admin-pagination span { color: var(--muted); font-size: 14px; }
    @media (max-width: 480px) { .admin-search > div { align-items: stretch; flex-direction: column; } }
</style>
