<?php
/** Project devlog posts, every status. @var array $game @var string $tab @var array $posts */
$gid = (int)$game['ma'];
$isPublic = in_array($game['trang_thai'], ['published', 'unlisted'], true);
?>
<?php partial('dev-game-tabs', ['game' => $game, 'tab' => $tab]); ?>

<div class="title-row">
    <div><h2><?= te('devlog.title') ?></h2><p class="sub"><?= te('dev.game-devlogs.chia_se_tien_do_voi') ?></p></div>
    <a class="btn primary" href="<?= e(app_url('/dev/games/' . $gid . '/devlogs/new')) ?>"><?= te('dev.game-devlogs.viet_bai') ?></a>
</div>

<?php if (!$posts): ?>
    <div class="empty"><p><?= te('dev.game-devlogs.chua_co_bai_nhat_ky') ?></p></div>
<?php else: ?>
<section class="panel flush">
    <table>
        <thead><tr><th scope="col"><?= te('forum.posts') ?></th><th scope="col"><?= te('common.status') ?></th><th scope="col" class="num"><?= te('devlog.comments') ?></th><th scope="col"><?= te('admin.games.cap_nhat') ?></th></tr></thead>
        <tbody>
        <?php foreach ($posts as $p): ?>
            <tr>
                <td>
                    <a class="link" href="<?= e(app_url('/dev/games/' . $gid . '/devlogs/' . (int)$p['ma'])) ?>"><?= e($p['tieu_de']) ?></a>
                    <span class="sub"><?= e($p['ten_hien_thi']) ?><?= $p['ngay_cong_bo'] ? ' ' . te('dev.game-devlogs.dot_dang') . ' ' . e(fmt_date($p['ngay_cong_bo'])) : '' ?>
                    <?php if ($p['trang_thai'] === 'published' && $isPublic): ?> · <a class="link" href="<?= e(app_url('/games/' . rawurlencode($game['duong_dan_rut_gon']) . '/devlogs/' . rawurlencode($p['duong_dan_rut_gon']))) ?>">Xem</a><?php endif; ?></span>
                </td>
                <td><?= status_pill('content_status', $p['trang_thai']) ?></td>
                <td class="num"><?= fmt_int((int)$p['so_binh_luan']) ?></td>
                <td><?= e(fmt_datetime($p['ngay_cap_nhat'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php endif; ?>
