<?php
/** Collections hub. @var array $mine @var array $community @var int $page @var int $pages @var int $total */
$user = current_user();
$card = function (array $c, bool $showOwner) {
    $cover = app_media_url((string)($c['anh_bia'] ?? ''));
    ?>
    <a class="game-card" href="<?= e(app_url('/collections/' . (int)$c['ma'])) ?>">
        <div class="cover">
            <?php if ($cover !== ''): ?><img src="<?= e($cover) ?>" alt="" loading="lazy" data-fallback="<?= e($c['tieu_de']) ?>"><?php else: ?><div class="cover-fallback"><?= e($c['tieu_de']) ?></div><?php endif; ?>
        </div>
        <h3><?= e($c['tieu_de']) ?></h3>
        <div class="meta-line">
            <span class="by"><?= te('collection.index.tro-choi', [ fmt_int((int)$c['so_game']) ,  $showOwner ? ' ' . te('common.dot') . ' ' . e($c['ten_hien_thi']) : '' ]) ?></span>
            <?php if (!$showOwner && $c['pham_vi_hien_thi'] !== 'public'): ?><span class="small faint"><?= e(label('visibility', $c['pham_vi_hien_thi'])) ?></span><?php endif; ?>
        </div>
        <?php if ($c['mo_ta']): ?><p class="blurb"><?= e(excerpt($c['mo_ta'], 120)) ?></p><?php endif; ?>
    </a>
    <?php
};
?>
<div class="title-row">
    <div>
        <h1><?= te('collection.title') ?></h1>
        <p class="sub"><?= te('collection.index.danh_sach_tro_choi_do') ?></p>
    </div>
</div>

<?php if ($user): ?>
<div class="grid section">
    <section class="span-8" aria-labelledby="mine-h">
        <div class="section-head"><h2 id="mine-h"><?= te('collection.index.cua_ban') ?></h2><span class="meta"><?= te('collection.index.bo-suu-tap', [ fmt_int(count($mine)) ]) ?></span></div>
        <?php if ($mine): ?>
            <div class="games-grid">
                <?php foreach ($mine as $c) $card($c, false); ?>
            </div>
        <?php else: ?>
            <div class="empty"><p><?= te('collection.index.ban_chua_co_bo_suu') ?></p></div>
        <?php endif; ?>
    </section>
    <aside class="span-4">
        <section class="panel" aria-labelledby="new-h" id="new">
            <header><h2 id="new-h"><?= te('collection.create') ?></h2></header>
            <form class="form" method="post" action="<?= e(app_url('/collections')) ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label for="c-title"><?= te('common.name') ?></label>
                    <input class="input" id="c-title" name="tieu_de" maxlength="180" required value="<?= e(old('tieu_de')) ?>" placeholder="<?= te('collection.index.vi_du_choi_cung_ban') ?>">
                </div>
                <div class="field">
                    <label for="c-desc"><?= te('collection.index.mo_ta_khong_bat_buoc') ?></label>
                    <textarea class="input" id="c-desc" name="mo_ta" maxlength="5000"><?= e(old('mo_ta')) ?></textarea>
                </div>
                <div class="field">
                    <label for="c-vis"><?= te('collection.index.ai_xem_duoc') ?></label>
                    <select class="input" id="c-vis" name="pham_vi_hien_thi">
                        <?php foreach (labels('visibility') as $v => $t): ?><option value="<?= e($v) ?>"<?= old('pham_vi_hien_thi', 'public') === $v ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
                    </select>
                    <span class="hint"><?= te('collection.index.khong_liet_ke_ai_co') ?></span>
                </div>
                <input type="hidden" name="bo_cuc" value="grid">
                <div class="form-actions"><button class="btn primary" type="submit"><?= te('collection.create') ?></button></div>
            </form>
        </section>
    </aside>
</div>
<?php endif; ?>

<section class="section" aria-labelledby="community-h">
    <div class="section-head"><h2 id="community-h"><?= te('collection.index.tu_cong_dong') ?></h2><span class="meta"><?= te('collection.index.bo-suu-tap-cong-khai', [ fmt_int($total) ]) ?></span></div>
    <?php if ($community): ?>
        <div class="games-grid">
            <?php foreach ($community as $c) $card($c, true); ?>
        </div>
        <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
    <?php else: ?>
        <div class="empty"><p><?= te('collection.index.chua_co_bo_suu_tap') ?></p><?php if (!$user): ?><a class="btn" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode('/collections'))) ?>"><?= te('collection.index.dang_nhap_de_tao_bo') ?></a><?php endif; ?></div>
    <?php endif; ?>
</section>
