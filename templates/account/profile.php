<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section class="account-profile">
    <p><a href="<?php echo app_url('/account'); ?>">← Tài khoản</a></p>
    <div class="card profile-card">
        <?php if (!empty($user['url_anh_dai_dien'])): ?>
            <img class="profile-avatar" src="<?php echo $escape($user['url_anh_dai_dien']); ?>" alt="Ảnh đại diện của <?php echo $escape($user['ten_hien_thi']); ?>">
        <?php else: ?>
            <div class="profile-avatar profile-avatar-empty" aria-hidden="true">✦</div>
        <?php endif; ?>
        <div>
            <h2><?php echo $escape($user['ten_hien_thi']); ?></h2>
            <p class="profile-username">@<?php echo $escape($user['ten_dang_nhap']); ?></p>
        </div>
    </div>

    <section class="card profile-info">
        <h3>Thông tin tài khoản</h3>
        <dl>
            <?php if (!empty($user['ma_quoc_gia'])): ?>
                <div><dt>Quốc gia</dt><dd><?php echo $escape($user['ma_quoc_gia']); ?></dd></div>
            <?php endif; ?>
            <?php if (!empty($user['url_website'])): ?>
                <div><dt>Website</dt><dd><?php echo $escape($user['url_website']); ?></dd></div>
            <?php endif; ?>
            <div><dt>Ngày tham gia</dt><dd><?php echo $escape(date('d/m/Y', strtotime($user['ngay_tao']))); ?></dd></div>
        </dl>
        <?php if (!empty($user['gioi_thieu'])): ?>
            <h3>Giới thiệu</h3>
            <p class="profile-bio"><?php echo nl2br($escape($user['gioi_thieu'])); ?></p>
        <?php endif; ?>
    </section>
</section>
<style>
    .account-profile { max-width: 820px; margin: auto; }
    .profile-card { display: flex; align-items: center; gap: 20px; margin: 20px 0; }
    .profile-avatar { width: 96px; height: 96px; flex: 0 0 96px; border-radius: 50%; object-fit: cover; }
    .profile-avatar-empty { display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, var(--purple), #a36cff); font-size: 38px; font-weight: 750; }
    .profile-card h2, .profile-card p { margin: 0; }
    .profile-username { margin-top: 6px !important; color: var(--muted); }
    .profile-info h3:first-child { margin-top: 0; }
    .profile-info dl { margin: 0; }
    .profile-info dl > div { display: grid; grid-template-columns: minmax(120px, 180px) minmax(0, 1fr); gap: 16px; padding: 12px 0; border-bottom: 1px solid var(--line); }
    .profile-info dt { color: var(--muted); font-weight: 650; }
    .profile-info dd { margin: 0; overflow-wrap: anywhere; }
    .profile-bio { white-space: pre-wrap; }
    @media (max-width: 480px) {
        .profile-card { align-items: flex-start; flex-direction: column; }
        .profile-info dl > div { grid-template-columns: 1fr; gap: 4px; }
    }
</style>
