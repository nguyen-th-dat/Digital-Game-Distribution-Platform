<?php
/** Discount campaign list. @var array $campaigns */
$pct = static fn($p): string => rtrim(rtrim(number_format((float)$p, 2, ',', ''), '0'), ',') . '%';
?>
<div class="title-row">
    <div><h1><?= te('admin.campaigns') ?></h1><p class="sub"><?= te('admin.campaigns.giam_theo_phan_tram_cho') ?></p></div>
    <a class="btn primary" href="<?= e(app_url('/admin/campaigns/new')) ?>"><?= te('admin.campaigns.tao_chien_dich') ?></a>
</div>

<?php if ($campaigns): ?>
<section class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th scope="col"><?= te('admin.campaigns.chien_dich') ?></th><th scope="col" class="num"><?= te('admin.campaigns.giam') ?></th><th scope="col"><?= te('admin.campaigns.thoi_gian') ?></th><th scope="col"><?= te('common.status') ?></th><th scope="col" class="num"><?= te('admin.games') ?></th></tr></thead>
            <tbody>
            <?php foreach ($campaigns as $c): ?>
                <tr>
                    <td class="lead"><a class="link" href="<?= e(app_url('/admin/campaigns/' . (int)$c['ma'])) ?>"><?= e($c['tieu_de']) ?></a><span class="sub">#<?= (int)$c['ma'] ?> · <?= e($c['nguoi_tao']) ?></span></td>
                    <td class="num"><?= e($pct($c['phan_tram_giam_gia'])) ?></td>
                    <td class="when"><?= e(fmt_datetime($c['ngay_bat_dau'])) ?><span class="sub"><?= te('admin.campaigns.den', [ e(fmt_datetime($c['ngay_ket_thuc'])) ]) ?></span></td>
                    <td><?= status_pill('campaign_status', DiscountCampaignModel::phase($c)) ?></td>
                    <td class="num"><?= fmt_int((int)$c['so_tro_choi']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
</section>
<?php else: ?>
    <div class="empty"><p><?= te('admin.campaigns.chua_co_chien_dich_giam') ?></p><a class="btn primary" href="<?= e(app_url('/admin/campaigns/new')) ?>"><?= te('admin.campaigns.tao_chien_dich_dau_tien') ?></a></div>
<?php endif; ?>
