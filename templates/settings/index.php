<?php
/** Account settings. @var array $me @var array $settings @var bool $isDeveloper */
$val = static fn(string $key, $fallback) => old($key, $fallback);
?>
<div class="title-row">
    <div>
        <h1><?= te('nav.settings') ?></h1>
        <p class="sub"><?= te('settings.index.dang_nhap_la') ?> <strong><?= e($me['ten_dang_nhap']) ?></strong> · <?= e($me['email']) ?> · <a class="link" href="<?= e(profile_url($me)) ?>"><?= te('admin.user.xem_ho_so_cong_khai') ?></a></p>
    </div>
</div>

<section class="settings-block" id="profile" aria-labelledby="profile-h">
    <div class="about">
        <h2 id="profile-h"><?= te('nav.public_profile') ?></h2>
        <p><?= te('settings.index.hien_thi_tren_trang_ho') ?></p>
    </div>
    <form class="panel form" method="post" action="<?= e(app_url('/settings/profile')) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="cluster">
            <img class="avatar lg" src="<?= e(avatar_url($me)) ?>" alt="<?= te('settings.index.anh_dai_dien_hien_tai') ?>">
            <div class="field grow">
                <label for="avatar"><?= te('settings.avatar') ?></label>
                <input class="input" id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif">
                <span class="hint"><?= te('settings.index.jpg_png_webp_hoac_gif') ?></span>
                <?php if ($me['url_anh_dai_dien']): ?><label class="check"><input type="checkbox" name="remove_avatar" value="1"> <?= te('settings.index.go_anh_dung_chu_cai') ?></label><?php endif; ?>
            </div>
        </div>
        <div class="field">
            <label for="cover"><?= te('settings.index.anh_bia_ho_so') ?></label>
            <input class="input" id="cover" type="file" name="cover" accept="image/jpeg,image/png,image/webp,image/gif">
            <span class="hint"><?= te('settings.index.toi-da-5-mb-ti-le-khoang-4-1', [ $me['url_anh_bia'] ? ' ' . te('settings.index.dang_co_anh_bia') : '' ]) ?></span>
            <?php if ($me['url_anh_bia']): ?><label class="check"><input type="checkbox" name="remove_cover" value="1"> <?= te('settings.index.go_anh_bia') ?></label><?php endif; ?>
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="ten_hien_thi"><?= te('settings.display_name') ?></label>
                <input class="input" id="ten_hien_thi" name="ten_hien_thi" value="<?= e($val('ten_hien_thi', $me['ten_hien_thi'])) ?>" required maxlength="100" autocomplete="nickname">
            </div>
            <div class="field">
                <label for="ma_quoc_gia"><?= te('settings.index.quoc_gia') ?></label>
                <input class="input" id="ma_quoc_gia" name="ma_quoc_gia" value="<?= e($val('ma_quoc_gia', $me['ma_quoc_gia'])) ?>" maxlength="2" pattern="[A-Za-z]{2}" placeholder="VN" autocomplete="country">
                <span class="hint"><?= te('settings.index.ma_2_chu_cai_theo') ?></span>
            </div>
            <div class="field full">
                <label for="url_website">Website</label>
                <input class="input" id="url_website" type="url" name="url_website" value="<?= e($val('url_website', $me['url_website'])) ?>" maxlength="500" placeholder="https://" autocomplete="url">
            </div>
            <div class="field full">
                <label for="gioi_thieu"><?= te('game.about') ?></label>
                <textarea class="input" id="gioi_thieu" name="gioi_thieu" rows="5" maxlength="2000"><?= e($val('gioi_thieu', $me['gioi_thieu'])) ?></textarea>
                <span class="hint"><?= te('settings.index.toi_da_2000_ky_tu') ?></span>
            </div>
        </div>
        <div class="form-actions"><button class="btn primary" type="submit"><?= te('settings.index.luu_ho_so') ?></button></div>
    </form>
</section>

<section class="settings-block" id="password" aria-labelledby="password-h">
    <div class="about">
        <h2 id="password-h"><?= te('auth.password') ?></h2>
        <p><?= te('settings.index.doi_mat_khau_se_tao') ?></p>
    </div>
    <form class="panel form" method="post" action="<?= e(app_url('/settings/password')) ?>">
        <?= csrf_field() ?>
        <input type="text" name="username" value="<?= e($me['ten_dang_nhap']) ?>" autocomplete="username" hidden>
        <div class="field">
            <label for="current_password"><?= te('auth.current_password') ?></label>
            <input class="input" id="current_password" type="password" name="current_password" required maxlength="72" autocomplete="current-password">
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="new_password"><?= te('auth.new_password') ?></label>
                <input class="input" id="new_password" type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password">
                <span class="hint"><?= te('settings.index.8_72_ky_tu') ?></span>
            </div>
            <div class="field">
                <label for="confirm_password"><?= te('auth.reset-password.nhap_lai_mat_khau_moi') ?></label>
                <input class="input" id="confirm_password" type="password" name="confirm_password" required minlength="8" maxlength="72" autocomplete="new-password">
            </div>
        </div>
        <div class="form-actions"><button class="btn primary" type="submit"><?= te('settings.index.doi_mat_khau') ?></button></div>
    </form>
</section>

<section class="settings-block" id="privacy" aria-labelledby="privacy-h">
    <div class="about">
        <h2 id="privacy-h"><?= te('settings.index.quyen_rieng_tu_va_tuy') ?></h2>
        <p><?= te('settings.index.ho_so_rieng_tu_chi') ?></p>
    </div>
    <form class="panel form" method="post" action="<?= e(app_url('/settings/privacy')) ?>">
        <?= csrf_field() ?>
        <fieldset class="field">
            <legend class="label"><?= te('settings.index.ai_xem_duoc_ho_so') ?></legend>
            <label class="check"><input type="radio" name="pham_vi_hien_thi_ho_so" value="public"<?= $settings['pham_vi_hien_thi_ho_so'] !== 'private' ? ' checked' : '' ?>><span><?= te('settings.index.moi_nguoi') ?><span class="sub"><?= te('settings.index.gioi_thieu_tro_choi_da') ?></span></span></label>
            <label class="check"><input type="radio" name="pham_vi_hien_thi_ho_so" value="private"<?= $settings['pham_vi_hien_thi_ho_so'] === 'private' ? ' checked' : '' ?>><span><?= te('settings.index.chi_minh_toi') ?></span></label>
        </fieldset>
        <label class="check"><input type="checkbox" name="hien_thi_game_so_huu" value="1"<?= (int)$settings['hien_thi_game_so_huu'] ? ' checked' : '' ?>><span><?= te('settings.index.hien_thu_vien_tro_choi') ?><span class="sub"><?= te('settings.index.ap_dung_khi_ho_so') ?></span></span></label>
        <label class="check"><input type="checkbox" name="cho_phep_nhan_tin" value="1"<?= (int)$settings['cho_phep_nhan_tin'] ? ' checked' : '' ?>><span><?= te('settings.index.cho_phep_nguoi_khac_nhan') ?></span></label>
        <label class="check"><input type="checkbox" name="email_cap_nhat_san_pham" value="1"<?= (int)$settings['email_cap_nhat_san_pham'] ? ' checked' : '' ?>><span><?= te('settings.index.nhan_email_khi_tro_choi') ?></span></label>
        <label class="check"><input type="checkbox" name="email_cap_nhat_cong_dong" value="1"<?= (int)$settings['email_cap_nhat_cong_dong'] ? ' checked' : '' ?>><span><?= te('settings.index.nhan_email_ve_dien_dan') ?></span></label>
        <div class="form-grid">
            <div class="field">
                <label for="ngon_ngu_uu_tien"><?= te('common.language') ?></label>
                <select class="input" id="ngon_ngu_uu_tien" name="ngon_ngu_uu_tien">
                    <option value="vi"<?= $settings['ngon_ngu_uu_tien'] !== 'en' ? ' selected' : '' ?>><?= te('settings.index.tieng_viet') ?></option>
                    <option value="en"<?= $settings['ngon_ngu_uu_tien'] === 'en' ? ' selected' : '' ?>>English</option>
                </select>
                <span class="hint"><?= te('settings.index.giao_dien_hien_chi_co') ?></span>
            </div>
            <div class="field">
                <label for="tien_te_uu_tien"><?= te('settings.index.tien_te_hien_thi') ?></label>
                <select class="input" id="tien_te_uu_tien" name="tien_te_uu_tien">
                    <option value="USD"<?= $settings['tien_te_uu_tien'] !== 'VND' ? ' selected' : '' ?>>USD</option>
                    <option value="VND"<?= $settings['tien_te_uu_tien'] === 'VND' ? ' selected' : '' ?>>VND</option>
                </select>
                <span class="hint"><?= te('settings.index.thanh_toan_luon_tinh_bang') ?></span>
            </div>
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="giao_dien"><?= te('settings.appearance') ?></label>
                <select class="input" id="giao_dien" name="giao_dien">
                    <option value="light"<?= ($settings['giao_dien'] ?? THEME_DEFAULT) !== THEME_DARK ? ' selected' : '' ?>><?= te('settings.theme_light') ?></option>
                    <option value="dark"<?= ($settings['giao_dien'] ?? THEME_DEFAULT) === THEME_DARK ? ' selected' : '' ?>><?= te('settings.theme_dark') ?></option>
                </select>
                <span class="hint"><?= te('settings.index.nut_tren_thanh_dau_cung') ?></span>
            </div>
        </div>
        <div class="form-actions"><button class="btn primary" type="submit"><?= te('settings.index.luu_tuy_chon') ?></button></div>
    </form>
</section>

<section class="settings-block" id="developer" aria-labelledby="developer-h">
    <div class="about">
        <h2 id="developer-h"><?= te('settings.index.phat_hanh_tro_choi') ?></h2>
        <p><?= te('settings.index.nha-phat-trien-tao-trang-tro-choi-tai-ban-phat-hanh-phat-ma-kich-hoat-va-nhan-doanh-thu-sau-phi-nen-tang', [ e((string)round(PLATFORM_FEE_RATE * 100)) ]) ?></p>
    </div>
    <?php if ($isDeveloper): ?>
        <div class="panel form">
            <p><?= te('settings.index.tai_khoan_cua_ban_da') ?></p>
            <div class="form-actions"><a class="btn primary" href="<?= e(app_url('/dev')) ?>"><?= te('settings.index.mo_khu_nha_phat_trien') ?></a></div>
        </div>
    <?php else: ?>
        <form class="panel form" method="post" action="<?= e(app_url('/settings/developer')) ?>">
            <?= csrf_field() ?>
            <label class="check"><input type="checkbox" name="agree" value="1" required><span><?= te('settings.index.toi_co_quyen_phat_hanh') ?><span class="sub"><?= te('settings.index.doanh-thu-duoc-giu-ngay-truoc-khi-co-the-rut-nguoi-mua-duoc-hoan-tien-trong-ngay', [ (int)PAYOUT_HOLD_DAYS ,  (int)REFUND_WINDOW_DAYS ]) ?></span></span></label>
            <div class="form-actions"><button class="btn primary" type="submit"><?= te('settings.index.tro_thanh_nha_phat_trien') ?></button></div>
        </form>
    <?php endif; ?>
</section>
