<?php
// =============================================================================
// LOCALE CONTROLLER - switches the language of the interface.
// The choice is stored in the account preferences for a signed-in visitor and
// in a long-lived cookie for a guest, so the next page is already in the
// language the visitor picked.
// =============================================================================

class LocaleController extends Controller {
    public function switch(array $params = []) {
        $posted = (string)($_POST['locale'] ?? '');
        $locale = Translator::isSupported($posted) ? $posted : Translator::defaultLocale();
        Translator::set($locale);

        $this->back('/');
    }
}
