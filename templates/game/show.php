<?php
/** Game detail page. */
$user = current_user();
$slug = $game['duong_dan_rut_gon'];
$base = '/games/' . rawurlencode($slug);
$hex = fn($v) => is_string($v) && preg_match('/^#[0-9a-fA-F]{6}$/', $v) ? $v : null;
$vars = [];
$pageInk = $hex($settings['mau_chu'] ?? null);
// A game with its own background but no ink colour gets ink that reads on it.
$pageBg = $hex($settings['mau_nen'] ?? null);
if ($pageBg && !$pageInk) {
    $pageInk = theme_readable_ink($pageBg);
}
if ($pageBg) $vars[] = '--page-bg:' . $pageBg;
if ($pageInk) $vars[] = '--page-ink:' . $pageInk;
if ($c = $hex($settings['mau_lien_ket'] ?? null)) $vars[] = '--page-link:' . $c;
$pageLayout = in_array($settings['bo_cuc_trang'] ?? 'default', ['default', 'wide', 'minimal'], true) ? $settings['bo_cuc_trang'] : 'default';
$isFree = game_is_free($game);
$isPwyw = $game['mo_hinh_gia'] === 'pay_what_you_want' && !$isFree;
$price = GameModel::effectivePrice($game);
$suggested = $isPwyw ? max((float)$game['gia_toi_thieu'], (float)($game['gia_de_xuat'] ?? 0)) : $price;
$reviewTotal = array_sum($breakdown);
$canReview = $user && (int)$game['cho_phep_danh_gia'] && !$isAuthor;
$commentsOn = (int)$game['cho_phep_binh_luan'] && (int)($settings['bat_cong_dong'] ?? 1);
$showCreators = (int)($settings['hien_thi_nha_sang_tao'] ?? 1);
$isPublic = in_array($game['trang_thai'], ['published', 'unlisted'], true);
$playUrl = game_play_url($game);
$comment = function (array $c, bool $reply) use ($user, $base, $commentsOn) {
    $mine = $user && (int)$user['ma'] === (int)$c['nguoi_dung_id'];
    ?>
    <article class="comment<?= $reply ? ' reply' : '' ?>" id="comment-<?= (int)$c['ma'] ?>">
        <img class="avatar" src="<?= e(avatar_url($c)) ?>" alt="" loading="lazy">
        <div>
            <div class="meta"><a class="link" href="<?= e(profile_url($c)) ?>"><?= e($c['ten_hien_thi']) ?></a> · <?= e(time_ago($c['ngay_tao'])) ?></div>
            <div class="prose"><?= e_multiline($c['noi_dung']) ?></div>
            <div class="cluster small faint">
                <?php if (!$reply && $user && $commentsOn): ?>
                    <details class="edit">
                        <summary class="link-btn"><?= te('forum.reply') ?></summary>
                        <form method="post" action="<?= e(app_url($base . '/comments')) ?>" class="form reply-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="parent_id" value="<?= (int)$c['ma'] ?>">
                            <label class="sr-only" for="reply-<?= (int)$c['ma'] ?>"><?= te('devlog.show.noi_dung_tra_loi') ?></label>
                            <textarea class="input" id="reply-<?= (int)$c['ma'] ?>" name="noi_dung" maxlength="5000" required></textarea>
                            <div class="form-actions"><button class="btn sm primary" type="submit"><?= te('devlog.show.gui_tra_loi') ?></button></div>
                        </form>
                    </details>
                <?php endif; ?>
                <?php if ($mine || is_staff()): ?>
                    <form method="post" action="<?= e(app_url('/comments/' . (int)$c['ma'] . '/delete')) ?>" data-confirm="<?= te('game.show.xoa-binh-luan-nay') ?>">
                        <?= csrf_field() ?><?= return_field() ?>
                        <button class="link-btn neg" type="submit"><?= te('admin.delete') ?></button>
                    </form>
                <?php endif; ?>
                <?php if (!$mine) partial('report', ['type' => 'game_comment', 'id' => (int)$c['ma']]); ?>
            </div>
        </div>
    </article>
    <?php
};
?>
<div class="game-page layout-<?= e($pageLayout) ?><?= $vars ? ' themed' : '' ?><?= $hex($settings['mau_nen'] ?? null) ? ' has-bg' : '' ?>"<?= $vars ? ' style="' . e(implode(';', $vars)) . '"' : '' ?>>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>">
    <a href="<?= e(app_url('/')) ?>"><?= te('home.title') ?></a>
    <a href="<?= e(app_url('/games')) ?>"><?= te('game.explore') ?></a>
    <?php if ($categories): ?><a href="<?= e(app_url('/games?category=' . rawurlencode($categories[0]['duong_dan_rut_gon']))) ?>"><?= e($categories[0]['ten']) ?></a><?php endif; ?>
    <span><?= e($game['tieu_de']) ?></span>
</nav>

<?php if (!$isPublic): ?>
    <p class="notice game-notice"><?= te('game.show.trang-nay-dang-o-trang-thai-nen-chi-tac-gia-va-quan-tri-vien-nhin-thay', [ e(label('game_status', $game['trang_thai'])) ]) ?></p>
<?php endif; ?>

<div class="game-head">
    <div class="gallery" data-gallery>
        <div class="gallery-main">
            <?php if ($gallery): $first = $gallery[0]; ?>
                <img data-gallery-main src="<?= e(app_media_url($first['url_media'])) ?>" alt="<?= e($first['van_ban_thay_the'] ?: $game['tieu_de']) ?>" data-fallback="<?= e($game['tieu_de']) ?>">
            <?php else: ?>
                <div class="cover-fallback"><?= e($game['tieu_de']) ?></div>
            <?php endif; ?>
        </div>
        <?php if (count($gallery) > 1): ?>
            <div class="gallery-strip" role="group" aria-label="<?= te('game.show.anh_cua_tro_choi') ?>">
                <?php foreach ($gallery as $i => $m): ?>
                    <button type="button" data-gallery-thumb data-src="<?= e(app_media_url($m['url_media'])) ?>" data-alt="<?= e($m['van_ban_thay_the'] ?: $game['tieu_de']) ?>" data-credit="<?= e(media_credit($m['chu_thich'])[0]) ?>" data-credit-url="<?= e(media_credit($m['chu_thich'])[1]) ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="<?= te('game.show.xem-anh-i-1') ?>">
                        <img src="<?= e(app_media_url($m['url_media'])) ?>" alt="" loading="lazy" data-fallback="">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($gallery): ?><p class="gallery-credit"><span data-gallery-credit><?= e(media_credit($gallery[0]['chu_thich'])[0]) ?></span><a class="link" data-gallery-source href="<?= e(media_credit($gallery[0]['chu_thich'])[1]) ?>" target="_blank" rel="noopener noreferrer"<?= media_credit($gallery[0]['chu_thich'])[1] === '' ? ' hidden' : '' ?>><?= te('common.photo_source') ?></a></p><?php endif; ?>
    </div>

    <aside class="buy-card" aria-label="<?= te('game.show.mua_va_thong_tin') ?>">
        <div>
            <h1><?= e($game['tieu_de']) ?></h1>
            <?php if ($game['mo_ta_ngan']): ?><p class="lede"><?= e($game['mo_ta_ngan']) ?></p><?php endif; ?>
        </div>
        <?php if ($reviewTotal): ?>
            <a class="rating" href="#reviews"><?= stars((float)$game['diem_tb']) ?> <strong><?= e(number_format((float)$game['diem_tb'], 1)) ?></strong> <span class="faint"><?= te('game.show.danh-gia', [ fmt_int($reviewTotal) ]) ?></span></a>
        <?php endif; ?>

        <div class="buy-actions">
            <?php if ($owned): ?>
                <div class="split"><span class="owned"><?= te('game.show.da_co_trong_thu_vien') ?></span><a class="link small" href="<?= e(app_url('/library')) ?>"><?= te('game.show.mo_thu_vien') ?></a></div>
                <?php if ($playUrl): ?>
                    <a class="btn primary block" href="<?= e($playUrl) ?>" target="_blank" rel="noopener noreferrer"><?= te('game.downloads.choi_tren_trinh_duyet') ?></a>
                <?php endif; ?>
                <?php if ($downloadCount > 0): ?>
                    <a class="btn<?= $playUrl ? '' : ' primary' ?> block" href="<?= e(app_url($base . '/downloads')) ?>"><?= te('game.show.tai-ve', [ $downloadCount > 1 ? ' ' . te('common.dot') . ' ' . fmt_int($downloadCount) . ' ' . te('game.show.lien_ket') : '' ]) ?></a>
                <?php endif; ?>
            <?php elseif (!$isPublic): ?>
                <p class="muted small"><?= te('game.show.tro_choi_chua_mo_ban') ?></p>
            <?php endif; ?>

            <?php if (!$owned && $isPublic): ?>
                <div class="price-big"><?= price_html($game) ?></div>
                <?php if ($isFree): ?>
                    <?php if ($user): ?>
                        <form method="post" action="<?= e(app_url($base . '/claim')) ?>">
                            <?= csrf_field() ?>
                            <button class="btn primary block" type="submit"><?= te('game.show.them_vao_thu_vien_mien') ?></button>
                        </form>
                    <?php else: ?>
                        <a class="btn primary block" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($base))) ?>"><?= te('game.show.dang_nhap_de_nhan') ?></a>
                    <?php endif; ?>
                <?php elseif ($inCart): ?>
                    <a class="btn primary block" href="<?= e(app_url('/cart')) ?>"><?= te('game.show.da_trong_gio_hang_thanh') ?></a>
                <?php elseif ($user): ?>
                    <form method="post" action="<?= e(app_url('/cart/add')) ?>" class="form">
                        <?= csrf_field() ?><?= return_field() ?>
                        <input type="hidden" name="type" value="game">
                        <input type="hidden" name="id" value="<?= (int)$game['ma'] ?>">
                        <?php if ($isPwyw): ?>
                            <div class="field">
                                <label for="pwyw"><?= te('game.show.so_tien_ban_muon_tra') ?></label>
                                <input class="input" id="pwyw" name="amount" type="number" inputmode="decimal" step="0.01" min="<?= e(number_format((float)$game['gia_toi_thieu'], 2, '.', '')) ?>" max="1000" value="<?= e(number_format($suggested, 2, '.', '')) ?>" required>
                                <span class="hint"><?= te('game.show.toi-thieu', [ e(money($game['gia_toi_thieu'])) ,  $game['gia_de_xuat'] ? ' ' . te('game.show.dot_de_xuat') . ' ' . e(money($game['gia_de_xuat'])) : '' ]) ?></span>
                            </div>
                        <?php endif; ?>
                        <button class="btn primary block" type="submit"><?= te('game.show.them_vao_gio_hang') ?></button>
                    </form>
                <?php else: ?>
                    <a class="btn primary block" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($base))) ?>"><?= te('bundle.show.dang_nhap_de_mua') ?></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($user): ?>
                <div class="cluster">
                    <form method="post" action="<?= e(app_url($base . '/wishlist')) ?>">
                        <?= csrf_field() ?>
                        <button class="btn sm" type="submit" aria-pressed="<?= $wishlisted ? 'true' : 'false' ?>"><?= $wishlisted ? te('game.show.bo_khoi_danh_sach_mong') : te('game.add_wishlist') ?></button>
                    </form>
                    <form method="post" action="<?= e(app_url($base . '/follow')) ?>">
                        <?= csrf_field() ?>
                        <button class="btn sm" type="submit" aria-pressed="<?= $following ? 'true' : 'false' ?>"><?= $following ? te('profile.following') : te('game.show.theo_doi_cap_nhat') ?></button>
                    </form>
                    <?php if ($isAuthor): ?><a class="btn sm" href="<?= e(app_url('/dev/games/' . (int)$game['ma'])) ?>"><?= te('game.show.chinh_sua') ?></a><?php endif; ?>
                </div>
                <details class="edit collect">
                    <summary class="link-btn"><?= te('game.show.them_vao_bo_suu_tap') ?></summary>
                    <div class="well collect-list">
                        <?php foreach ($collections as $c): ?>
                            <?php if ((int)$c['co_game']): ?>
                                <div class="split small"><span><?= e($c['tieu_de']) ?></span><a class="link faint" href="<?= e(app_url('/collections/' . (int)$c['ma'])) ?>"><?= te('game.show.da_co') ?></a></div>
                            <?php else: ?>
                                <form class="split small" method="post" action="<?= e(app_url('/collections/' . (int)$c['ma'] . '/items')) ?>">
                                    <?= csrf_field() ?><?= return_field($base) ?>
                                    <input type="hidden" name="game_id" value="<?= (int)$game['ma'] ?>">
                                    <span><?= e($c['tieu_de']) ?></span>
                                    <button class="btn sm" type="submit"><?= te('common.more') ?></button>
                                </form>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <form class="form" method="post" action="<?= e(app_url('/collections')) ?>">
                            <?= csrf_field() ?><?= return_field($base) ?>
                            <input type="hidden" name="game_id" value="<?= (int)$game['ma'] ?>">
                            <input type="hidden" name="pham_vi_hien_thi" value="public"><input type="hidden" name="bo_cuc" value="grid">
                            <label class="small" for="new-collection"><?= te('game.show.bo_suu_tap_moi') ?></label>
                            <div class="cluster">
                                <input class="input sm grow" id="new-collection" name="tieu_de" maxlength="180" required placeholder="<?= te('collection.name_placeholder') ?>">
                                <button class="btn sm" type="submit"><?= te('game.show.tao_va_them') ?></button>
                            </div>
                        </form>
                    </div>
                </details>
            <?php endif; ?>
        </div>

        <dl class="facts">
            <?php if ($showCreators && $authors): ?>
                <dt><?= te('common.developer') ?></dt>
                <dd><?php foreach ($authors as $i => $a): ?><?= $i ? ', ' : '' ?><a href="<?= e(profile_url($a)) ?>"><?= e($a['ten_hien_thi']) ?></a><?php endforeach; ?></dd>
            <?php endif; ?>
            <?php if ($showCreators && $publisher): ?>
                <dt><?= te('game.show.phat_hanh_boi') ?></dt><dd><a href="<?= e(profile_url($publisher)) ?>"><?= e($publisher['ten_hien_thi']) ?></a></dd>
            <?php endif; ?>
            <dt><?= te('game.index.giai_doan') ?></dt><dd><?= e(label('release_status', $game['trang_thai_phat_hanh'])) ?></dd>
            <dt><?= te('common.type') ?></dt><dd><?= e(label('project_type', $game['loai_du_an'])) ?></dd>
            <?php if ($platforms): ?>
                <dt><?= te('common.platform') ?></dt>
                <dd><?php foreach ($platforms as $i => $p): ?><?= $i ? ', ' : '' ?><a href="<?= e(app_url('/games?platform=' . rawurlencode($p['ma_nen_tang']))) ?>"><?= e($p['ten_nen_tang']) ?></a><?php endforeach; ?></dd>
            <?php endif; ?>
            <?php if ($categories): ?>
                <dt><?= te('common.categories') ?></dt>
                <dd><?php foreach ($categories as $i => $c): ?><?= $i ? ', ' : '' ?><a href="<?= e(app_url('/games?category=' . rawurlencode($c['duong_dan_rut_gon']))) ?>"><?= e($c['ten']) ?></a><?php endforeach; ?></dd>
            <?php endif; ?>
            <?php if ($game['ten_giay_phep']): ?><dt><?= te('game.license') ?></dt><dd><?= e($game['ten_giay_phep']) ?></dd><?php endif; ?>
            <?php if ($game['phan_loai_do_tuoi']): ?><dt><?= te('game.show.do_tuoi') ?></dt><dd><?= e($game['phan_loai_do_tuoi']) ?></dd><?php endif; ?>
            <?php if ($game['ngay_cong_bo']): ?><dt><?= te('game.show.cong_bo') ?></dt><dd><?= e(fmt_date($game['ngay_cong_bo'])) ?></dd><?php endif; ?>
            <?php if ($releases): ?><dt><?= te('dev.release_version') ?></dt><dd><?= e($releases[0]['ten_phien_ban']) ?> · <?= e(label('channel', $releases[0]['kenh_phat_hanh'])) ?></dd><?php endif; ?>
            <dt><?= te('dev.game-stats.nguoi_so_huu') ?></dt><dd><?= fmt_int($ownerCount) ?></dd>
        </dl>
    </aside>
</div>

<div class="game-body">
    <div class="game-main">
        <section class="section-flat" aria-labelledby="about">
            <h2 id="about" class="block-head"><?= te('game.about') ?></h2>
            <?php if (trim((string)$game['mo_ta']) !== ''): ?>
                <div class="prose"><?= e_multiline($game['mo_ta']) ?></div>
            <?php else: ?>
                <p class="muted"><?= te('game.show.nha_phat_trien_chua_viet') ?></p>
            <?php endif; ?>
            <?php if ($links): ?>
                <ul class="link-list">
                    <?php foreach ($links as $l): if (!preg_match('#^https?://#i', $l['url_media'])) continue; ?>
                        <li><a class="link" href="<?= e($l['url_media']) ?>" target="_blank" rel="noopener noreferrer"><?= e($l['chu_thich'] ?: label('media_type', $l['loai_media'])) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <?php if ($devlogs): ?>
            <section class="section" aria-labelledby="devlogs">
                <div class="section-head"><h2 id="devlogs"><?= te('devlog.title') ?></h2><a class="meta link" href="<?= e(app_url($base . '/devlogs')) ?>"><?= te('game.show.tat-ca-bai', [ fmt_int($devlogCount) ]) ?></a></div>
                <div class="rows">
                    <?php foreach ($devlogs as $d): ?>
                        <a class="row row-link" href="<?= e(app_url($base . '/devlogs/' . rawurlencode($d['duong_dan_rut_gon']))) ?>">
                            <span class="grow"><?= e($d['tieu_de']) ?><span class="sub"><?= e(fmt_date($d['ngay_cong_bo'])) ?> · <?= e(excerpt($d['noi_dung'], 110)) ?></span></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="section" id="reviews" aria-labelledby="reviews-h">
            <div class="section-head">
                <h2 id="reviews-h"><?= te('game.reviews') ?></h2>
                <?php if ($reviews): ?>
                    <div class="cluster small">
                        <a class="link<?= $reviewSort === 'recent' ? ' is-on' : '' ?>" href="<?= e(app_url($base . qs(['reviews' => null]) . '#reviews')) ?>"<?= $reviewSort === 'recent' ? ' aria-current="true"' : '' ?>><?= te('game.sort_new') ?></a>
                        <a class="link<?= $reviewSort === 'helpful' ? ' is-on' : '' ?>" href="<?= e(app_url($base . qs(['reviews' => 'helpful']) . '#reviews')) ?>"<?= $reviewSort === 'helpful' ? ' aria-current="true"' : '' ?>><?= te('game.show.huu_ich_nhat') ?></a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($reviewTotal): ?>
                <div class="review-summary">
                    <div class="score"><strong><?= e(number_format((float)$game['diem_tb'], 1)) ?></strong><span class="faint small"><?= te('game.show.danh-gia', [ fmt_int($reviewTotal) ]) ?></span></div>
                    <div class="breakdown">
                        <?php for ($s = 5; $s >= 1; $s--): $n = $breakdown[$s]; $pct = $reviewTotal ? round($n / $reviewTotal * 100) : 0; ?>
                            <div class="bar-row"><span><?= $s ?> sao</span><span class="bar"><span class="fill w-<?= (int)(round($pct / 5) * 5) ?>"></span></span><span class="n"><?= fmt_int($n) ?></span></div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($canReview): ?>
                <details class="edit review-form"<?= $myReview ? '' : ' open' ?>>
                    <summary class="btn sm"><?= $myReview ? te('game.show.sua_danh_gia_cua_ban') : te('game.write_review') ?></summary>
                    <form method="post" action="<?= e(app_url($base . '/reviews')) ?>" class="form well">
                        <?= csrf_field() ?>
                        <fieldset class="field">
                            <legend class="label"><?= te('game.show.diem_cua_ban') ?></legend>
                            <div class="star-input">
                                <?php for ($s = 5; $s >= 1; $s--): ?>
                                    <input type="radio" id="star-<?= $s ?>" name="diem" value="<?= $s ?>"<?= (int)($myReview['diem_danh_gia'] ?? 0) === $s ? ' checked' : '' ?> required>
                                    <label for="star-<?= $s ?>" title="<?= $s ?> sao"><?= $s ?></label>
                                <?php endfor; ?>
                            </div>
                        </fieldset>
                        <div class="field">
                            <label for="review-title"><?= te('game.show.tieu_de_khong_bat_buoc') ?></label>
                            <input class="input" id="review-title" name="tieu_de" maxlength="200" value="<?= e($myReview['tieu_de'] ?? '') ?>">
                        </div>
                        <div class="field">
                            <label for="review-body"><?= te('game.show.nhan_xet') ?></label>
                            <textarea class="input" id="review-body" name="noi_dung" maxlength="10000"><?= e($myReview['noi_dung_danh_gia'] ?? '') ?></textarea>
                            <?php if (!$owned): ?><span class="hint"><?= te('game.show.ban_chua_so_huu_tro') ?></span><?php endif; ?>
                        </div>
                        <div class="form-actions">
                            <button class="btn primary" type="submit"><?= $myReview ? te('game.show.cap_nhat_danh_gia') : te('game.show.gui_danh_gia') ?></button>
                        </div>
                    </form>
                    <?php if ($myReview): ?>
                        <form method="post" action="<?= e(app_url($base . '/reviews/delete')) ?>" data-confirm="<?= te('game.show.xoa-danh-gia-cua-ban') ?>" class="review-delete">
                            <?= csrf_field() ?>
                            <button class="link-btn neg" type="submit"><?= te('game.show.xoa_danh_gia') ?></button>
                        </form>
                    <?php endif; ?>
                </details>
                <?php if ($myReview && $myReview['trang_thai'] !== 'published'): ?>
                    <p class="notice"><?= te('game.show.danh-gia-cua-ban-dang-o-trang-thai-do-dieu-hanh-vien-xu-ly', [ e(label('content_status', $myReview['trang_thai'])) ]) ?></p>
                <?php endif; ?>
            <?php elseif (!$user && (int)$game['cho_phep_danh_gia']): ?>
                <p class="muted"><a class="link" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($base . '#reviews'))) ?>"><?= te('auth.login_button') ?></a> <?= te('game.show.de_viet_danh_gia') ?></p>
            <?php elseif (!(int)$game['cho_phep_danh_gia']): ?>
                <p class="muted"><?= te('game.show.nha_phat_trien_da_tat') ?></p>
            <?php endif; ?>

            <?php if ($reviews): ?>
                <div class="reviews">
                    <?php foreach ($reviews as $r): $mine = $user && (int)$r['nguoi_dung_id'] === (int)$user['ma']; ?>
                        <article class="review" id="review-<?= (int)$r['ma'] ?>">
                            <header>
                                <img class="avatar md" src="<?= e(avatar_url($r)) ?>" alt="" loading="lazy">
                                <div class="who"><a class="link" href="<?= e(profile_url($r)) ?>"><?= e($r['ten_hien_thi']) ?></a><span><?= e(fmt_date($r['ngay_tao'])) ?><?= (int)$r['da_xac_minh_so_huu'] ? ' ' . te('common.dot_da_so_huu') : '' ?></span></div>
                                <?= stars((float)$r['diem_danh_gia']) ?>
                            </header>
                            <?php if ($r['tieu_de']): ?><h4><?= e($r['tieu_de']) ?></h4><?php endif; ?>
                            <?php if ($r['noi_dung_danh_gia']): ?><div class="prose"><?= e_multiline($r['noi_dung_danh_gia']) ?></div><?php endif; ?>
                            <div class="actions">
                                <span><?= te('game.show.nguoi-thay-huu-ich', [ fmt_int($r['co_ich']) ]) ?></span>
                                <?php if ($user && !$mine): $vote = $r['phieu_cua_toi']; ?>
                                    <form method="post" action="<?= e(app_url('/reviews/' . (int)$r['ma'] . '/vote')) ?>" class="cluster">
                                        <?= csrf_field() ?><?= return_field() ?>
                                        <button class="react" type="submit" name="co_ich" value="1" aria-pressed="<?= $vote !== null && (int)$vote === 1 ? 'true' : 'false' ?>"><?= te('label.reaction.helpful') ?></button>
                                        <button class="react" type="submit" name="co_ich" value="0" aria-pressed="<?= $vote !== null && (int)$vote === 0 ? 'true' : 'false' ?>"><?= te('game.show.khong_huu_ich') ?></button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($user && !$mine) partial('report', ['type' => 'review', 'id' => (int)$r['ma']]); ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php elseif ((int)$game['cho_phep_danh_gia']): ?>
                <div class="empty"><?= te('game.show.chua_co_danh_gia_nao') ?></div>
            <?php endif; ?>
        </section>

        <?php if ($commentsOn): ?>
            <section class="section" id="comments" aria-labelledby="comments-h">
                <div class="section-head"><h2 id="comments-h"><?= te('devlog.comments') ?></h2><span class="meta"><?= te('game.show.binh-luan', [ fmt_int(count($comments) + array_sum(array_map('count', $replies))) ]) ?></span></div>
                <?php if ($user): ?>
                    <form method="post" action="<?= e(app_url($base . '/comments')) ?>" class="form comment-form">
                        <?= csrf_field() ?>
                        <label class="sr-only" for="comment-body"><?= te('devlog.show.binh_luan_cua_ban') ?></label>
                        <textarea class="input" id="comment-body" name="noi_dung" maxlength="5000" placeholder="<?= te('game.show.chia_se_cam_nhan_hoac') ?>" required></textarea>
                        <div class="form-actions"><button class="btn primary" type="submit"><?= te('game.post_comment') ?></button></div>
                    </form>
                <?php else: ?>
                    <p class="muted"><a class="link" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($base . '#comments'))) ?>"><?= te('auth.login_button') ?></a> <?= te('devlog.show.de_binh_luan') ?></p>
                <?php endif; ?>
                <?php if ($comments): ?>
                    <div class="comments">
                        <?php foreach ($comments as $c): ?>
                            <?php $comment($c, false); ?>
                            <?php foreach ($replies[(int)$c['ma']] ?? [] as $reply) $comment($reply, true); ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty"><?= te('game.no_comments') ?></div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>

    <aside class="game-rail stack" aria-label="<?= te('game.show.thong_tin_them') ?>">
        <?php if ($releases): ?>
            <section class="panel">
                <header><h2><?= te('dev.release_version') ?></h2></header>
                <div class="rows">
                    <?php foreach ($releases as $rel): ?>
                        <div class="row">
                            <span class="grow"><?= e($rel['ten_phien_ban']) ?><span class="sub"><?= e($rel['ngay_phat_hanh'] ? fmt_date($rel['ngay_phat_hanh']) : te('game.show.chua_dat_ngay')) ?></span></span>
                            <span class="pill"><?= e(label('channel', $rel['kenh_phat_hanh'])) ?></span>
                        </div>
                        <?php if ($rel['ghi_chu_phat_hanh']): ?><p class="small muted release-note"><?= e(excerpt($rel['ghi_chu_phat_hanh'], 180)) ?></p><?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($tags): ?>
            <section class="panel">
                <header><h2><?= te('common.tag') ?></h2></header>
                <div class="chips">
                    <?php foreach ($tags as $t): ?><a class="chip" href="<?= e(app_url('/games?tag=' . rawurlencode($t['duong_dan_rut_gon']))) ?>"><?= e($t['ten']) ?></a><?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($bundles): ?>
            <section class="panel">
                <header><h2><?= te('game.show.co_trong_goi') ?></h2></header>
                <div class="rows">
                    <?php foreach ($bundles as $b): ?>
                        <a class="row row-link" href="<?= e(app_url('/bundles/' . rawurlencode($b['duong_dan_rut_gon']))) ?>"><span class="grow"><?= e($b['tieu_de']) ?></span><span><?= (float)$b['gia'] <= 0 ? te('price.free') : e(money($b['gia'])) ?></span></a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($forum): ?>
            <section class="panel">
                <header><h2><?= te('nav.community') ?></h2></header>
                <a class="row row-link" href="<?= e(app_url('/forums/' . (int)$forum['ma'])) ?>"><span class="grow"><?= e($forum['tieu_de']) ?><span class="sub"><?= te('game.show.chu-de-thao-luan', [ fmt_int($forum['so_chu_de']) ]) ?></span></span></a>
            </section>
        <?php endif; ?>

        <?php if ($moreGames): ?>
            <section class="panel">
                <header><h2><?= te('game.show.cung_nha_phat_trien') ?></h2></header>
                <div class="rank-list">
                    <?php foreach ($moreGames as $mg): ?>
                        <a class="rank-row compact row-link" href="<?= e(game_url($mg)) ?>">
                            <?php if ($mg['anh_bia']): ?><img src="<?= e(app_media_url($mg['anh_bia'])) ?>" alt="" loading="lazy" data-fallback=""><?php else: ?><span class="thumb"></span><?php endif; ?>
                            <span class="rank-text"><span class="t"><?= e($mg['tieu_de']) ?></span><span class="sub"><?= e(price_label($mg)) ?></span></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($user && !$isAuthor): ?>
            <div class="small"><?php partial('report', ['type' => 'game', 'id' => (int)$game['ma'], 'labelText' => te('game.show.bao_cao_tro_choi_nay')]); ?></div>
        <?php endif; ?>
    </aside>
</div>
</div>
