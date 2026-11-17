<?php
/** Developer projects. @var array $games @var int $uid */
?>
<div class="title-row">
    <div><h1><?= te('dev.games') ?></h1><p class="sub"><?= te('dev.games.du_an_ban_tao_hoac') ?></p></div>
    <a class="btn primary" href="<?= e(app_url('/dev/games/new')) ?>"><?= te('dev.create_game') ?></a>
</div>

<?php if (!$games): ?>
    <div class="empty"><p><?= te('dev.games.ban_chua_co_du_an') ?></p><a class="btn primary" href="<?= e(app_url('/dev/games/new')) ?>"><?= te('dev.games.tao_du_an_dau_tien') ?></a></div>
<?php else: ?>
<section class="panel flush">
    <table>
        <thead><tr><th scope="col"><?= te('dev.games') ?></th><th scope="col"><?= te('common.status') ?></th><th scope="col"><?= te('common.price') ?></th><th scope="col" class="num"><?= te('dev.games.so_huu') ?></th><th scope="col" class="num"><?= te('dev.games.luot_xem_30_ngay') ?></th><th scope="col"><?= te('admin.roles') ?></th></tr></thead>
        <tbody>
        <?php foreach ($games as $g): ?>
            <tr>
                <td>
                    <div class="cell-game">
                        <?php if ($g['anh_bia']): ?><img src="<?= e(app_media_url($g['anh_bia'])) ?>" alt="" loading="lazy"><?php else: ?><span class="thumb"></span><?php endif; ?>
                        <div><a class="link" href="<?= e(app_url('/dev/games/' . (int)$g['ma'])) ?>"><?= e($g['tieu_de']) ?></a><span class="sub"><?= e(label('project_type', $g['loai_du_an'])) ?> · <?= e(label('release_status', $g['trang_thai_phat_hanh'])) ?></span></div>
                    </div>
                </td>
                <td><?= status_pill('game_status', $g['trang_thai']) ?></td>
                <td><?= e(label('pricing', $g['mo_hinh_gia'])) ?><?= (float)$g['gia_toi_thieu'] > 0 ? '<span class="sub">' . e(money($g['gia_toi_thieu'])) . '</span>' : '' ?></td>
                <td class="num"><?= fmt_int((int)$g['so_huu']) ?></td>
                <td class="num"><?= fmt_int((int)$g['luot_xem_30']) ?></td>
                <td><?= (int)$g['nguoi_tao_id'] === $uid ? te('dev.games.nguoi_tao') : te('dev.games.dong_tac_gia') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php endif; ?>
