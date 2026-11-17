<?php
// =============================================================================
// THEME CONTROLLER - light/dark appearance for the whole site.
// The choice is stored in the account preferences for a signed-in visitor and
// in a long-lived cookie for a guest, so the next page can paint the right
// palette without a flash of the wrong theme.
// =============================================================================

class ThemeController extends Controller {
    public function toggle(array $params = []) {
        // The form posts the theme it wants; without JavaScript it is the
        // opposite of the current one, so the button always changes something.
        $posted = (string)($_POST['theme'] ?? '');
        $theme = in_array($posted, [THEME_DARK, THEME_LIGHT], true) ? $posted : theme_toggle_target();
        theme_set($theme);

        // A signed-in visitor also gets the choice saved to their account.
        $uid = current_user_id();
        if ($uid > 0) {
            try {
                Model::run(
                    'INSERT INTO cai_dat_nguoi_dung (nguoi_dung_id, giao_dien) VALUES (:uid, :theme)
                     ON DUPLICATE KEY UPDATE giao_dien = VALUES(giao_dien)',
                    [':uid' => $uid, ':theme' => $theme]
                );
            } catch (Throwable $error) {
                // The column is missing on installs that have not run the theme migration.
                error_log('theme toggle: ' . $error->getMessage());
            }
        }

        $this->back('/');
    }
}
