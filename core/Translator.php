<?php
// =============================================================================
// TRANSLATOR - loads the JSON catalogues in translation/ and resolves keys
// =============================================================================

class Translator {
    /** The languages the interface is translated into. */
    public const LOCALES = ['vi', 'en'];

    /** The language every page falls back to when a key is missing. */
    public const FALLBACK = 'vi';

    /** Cookie that remembers the language of a visitor who is not signed in. */
    public const COOKIE = 'kgdl_locale';

    /** The catalogue currently in use. */
    protected static string $locale = 'vi';

    /** The loaded catalogues, keyed by locale. */
    protected static array $catalogues = [];

    /** The catalogue the current locale falls back to, when it is loaded. */
    protected static ?array $fallbackCatalogue = null;

    /** Set once the locale of this request has been resolved. */
    protected static bool $resolved = false;

    /** The locale resolved for this request. */
    public static function locale(): string {
        self::resolve();
        return self::$locale;
    }

    /**
     * The language of this request: the account preference of a signed-in
     * visitor, then the session, then the cookie, then the default language.
     *
     * The result is cached in the session together with the account it belongs
     * to, so a page costs one query at most and switching accounts never shows
     * the previous account's language.
     */
    protected static function resolve(): void {
        if (self::$resolved) {
            return;
        }
        self::$resolved = true;

        $uid = current_user_id();
        if ($uid > 0) {
            $cachedUid = $_SESSION['locale_uid'] ?? null;
            $cachedLocale = $_SESSION['locale'] ?? null;
            if ($cachedUid === $uid && is_string($cachedLocale) && self::isSupported($cachedLocale)) {
                self::$locale = $cachedLocale;
                return;
            }
            try {
                $stored = Model::scalar('SELECT ngon_ngu_uu_tien FROM cai_dat_nguoi_dung WHERE nguoi_dung_id = :uid', [':uid' => $uid]);
                if (is_string($stored) && self::isSupported($stored)) {
                    self::$locale = $stored;
                    return;
                }
            } catch (Throwable $error) {
                // The column is missing on installs that have not run the language migration.
                error_log('Translator::resolve: ' . $error->getMessage());
            }
        }

        $sessionLocale = $_SESSION['locale'] ?? null;
        if (is_string($sessionLocale) && self::isSupported($sessionLocale)) {
            self::$locale = $sessionLocale;
            return;
        }

        $cookieLocale = $_COOKIE[self::COOKIE] ?? null;
        if (is_string($cookieLocale) && self::isSupported($cookieLocale)) {
            self::$locale = $cookieLocale;
        }
    }

    /** Is this a language the interface knows? */
    public static function isSupported(?string $locale): bool {
        return is_string($locale) && in_array($locale, self::LOCALES, true);
    }

    /** The language every visitor gets until they choose another one. */
    public static function defaultLocale(): string {
        return self::FALLBACK;
    }

    /** The languages of the interface with their name in their own language. */
    public static function available(): array {
        return [
            'vi' => 'Tiếng Việt',
            'en' => 'English',
        ];
    }

    /**
     * Store the language for the rest of this session, for the next visit and
     * for the account of a signed-in visitor.
     */
    public static function set(?string $locale): string {
        $locale = self::isSupported($locale) ? $locale : self::defaultLocale();
        self::$locale = $locale;
        self::$resolved = true;
        self::cache($locale);

        $uid = current_user_id();
        if ($uid > 0) {
            try {
                Model::run('INSERT INTO cai_dat_nguoi_dung (nguoi_dung_id, ngon_ngu_uu_tien) VALUES (:u, :l)
                    ON DUPLICATE KEY UPDATE ngon_ngu_uu_tien = VALUES(ngon_ngu_uu_tien)', [':u' => $uid, ':l' => $locale]);
            } catch (Throwable $error) {
                error_log('Translator::set: ' . $error->getMessage());
            }
        }

        self::setCookie($locale);
        return $locale;
    }

    /** Remember the language in the session. */
    protected static function cache(string $locale): void {
        $_SESSION['locale'] = $locale;
        $_SESSION['locale_uid'] = current_user_id();
    }

    /** Remember the language in a cookie so guests keep it between visits. */
    public static function setCookie(string $locale): void {
        if (headers_sent()) {
            return;
        }
        setcookie(self::COOKIE, self::isSupported($locale) ? $locale : self::defaultLocale(), [
            'expires' => time() + 31536000,
            'path' => APP_BASE_URL === '' ? '/' : APP_BASE_URL . '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * The text of a key in the language of this request.
     *
     * A key that is missing in the current language falls back to the default
     * language, and a key that is missing everywhere is returned as it was
     * written, so a broken translation never blanks the page.
     */
    public static function get(string $key, array $params = []): string {
        $catalogue = self::catalogue(self::locale());
        $text = $catalogue[$key] ?? null;
        if ($text === null) {
            $text = self::fallbackCatalogue()[$key] ?? $key;
        }
        return self::interpolate((string)$text, $params);
    }

    /** The whole catalogue of a language. */
    protected static function catalogue(string $locale): array {
        if (!isset(self::$catalogues[$locale])) {
            self::$catalogues[$locale] = self::load($locale);
        }
        return self::$catalogues[$locale];
    }

    /** The catalogue of the default language, loaded on demand. */
    protected static function fallbackCatalogue(): array {
        if (self::$fallbackCatalogue === null) {
            self::$fallbackCatalogue = self::load(self::FALLBACK);
        }
        return self::$fallbackCatalogue;
    }

    /**
     * Read one JSON catalogue from translation/.
     *
     * A catalogue is either translation/{locale}.json or the directory
     * translation/{locale}/ with one JSON file per area; the areas of a
     * directory are merged, so a large interface stays in readable pieces.
     */
    protected static function load(string $locale): array {
        $file = APP_DIR . '/translation/' . $locale . '.json';
        if (is_file($file)) {
            return self::readJson($file);
        }

        $dir = APP_DIR . '/translation/' . $locale;
        if (!is_dir($dir)) {
            error_log('Translator: missing catalogue ' . $file);
            return [];
        }

        $merged = [];
        $files = glob($dir . '/*.json') ?: [];
        sort($files);
        foreach ($files as $path) {
            $merged = array_merge($merged, self::readJson($path));
        }
        return $merged;
    }

    /** Read one JSON file; a broken file never breaks the request. */
    protected static function readJson(string $path): array {
        $raw = file_get_contents($path);
        if ($raw === false) {
            error_log('Translator: cannot read ' . $path);
            return [];
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            error_log('Translator: broken JSON in ' . $path);
            return [];
        }
        return $data;
    }

    /** Replace {name} placeholders with the given values. */
    protected static function interpolate(string $text, array $params): string {
        if (!$params) {
            return $text;
        }
        $replacements = [];
        foreach ($params as $name => $value) {
            $replacements['{' . $name . '}'] = (string)$value;
        }
        return strtr($text, $replacements);
    }

    /** Is this key present in the current language? */
    public static function has(string $key): bool {
        return isset(self::catalogue(self::locale())[$key]);
    }

    /**
     * The part of the catalogue under one prefix, with the prefix removed.
     *
     * A catalogue file of the labels area holds the groups of labels, so
     * "label.game_status.draft" becomes "draft" in the group "game_status".
     */
    public static function section(string $prefix): array {
        $catalogue = self::catalogue(self::locale());
        $out = [];
        foreach ($catalogue as $key => $value) {
            if (str_starts_with((string)$key, $prefix . '.')) {
                $out[substr((string)$key, strlen($prefix) + 1)] = $value;
            }
        }
        return $out;
    }

    /** The keys of the current language that the default language also has. */
    public static function missingKeys(): array {
        $catalogue = self::catalogue(self::locale());
        $fallback = self::fallbackCatalogue();
        return array_values(array_filter(array_keys($catalogue), static fn(string $key): bool => !isset($fallback[$key])));
    }
}
