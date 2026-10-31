<?php
// =============================================================================
// AVATAR CONTROLLER - Generates default account avatars as SVG
// =============================================================================

class AvatarController extends Controller {

    public function show(array $params) {
        $userId = (int)($params['id'] ?? 0);
        $user = $userId > 0 ? UserModel::find($userId) : null;
        if (!$user) {
            http_response_code(404);
            exit;
        }

        $name = trim((string)$user['ten_hien_thi']);
        $words = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            if (preg_match('/^./us', $word, $match) === 1) {
                $initials .= $match[0];
            }
        }
        if ($initials === '') {
            $initials = '?';
        }

        $colors = ['#7047eb', '#d5527f', '#168b83', '#dc772f', '#4774c9', '#8a5a9e'];
        $color = $colors[hexdec(substr(hash('sha256', (string)$user['ma']), 0, 2)) % count($colors)];
        $safeInitials = htmlspecialchars($initials, ENT_QUOTES | ENT_XML1, 'UTF-8');

        header('Content-Type: image/svg+xml; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" role="img">'
            . '<rect width="256" height="256" rx="128" fill="' . $color . '"/>'
            . '<text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" '
            . 'fill="#fff" font-family="Arial,sans-serif" font-size="92" font-weight="700">'
            . $safeInitials . '</text></svg>';
        exit;
    }
}
