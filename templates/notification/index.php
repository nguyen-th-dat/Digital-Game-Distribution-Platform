<?php
/** @var array $notifications @var string $filter @var int $unread @var int $page @var int $pages @var int $total */
?>
<div class="title-row">
    <div>
        <h1><?= te('nav.notifications') ?></h1>
        <p class="sub"><?= $unread ? fmt_int($unread) . ' ' . te('notification.index.chua_doc') : te('notification.index.ban_da_doc_het') ?></p>
    </div>
    <?php if ($unread): ?>
        <form class="actions" method="post" action="<?= e(app_url('/notifications/read-all')) ?>"><?= csrf_field() ?><button class="btn" type="submit"><?= te('notification.index.danh_dau_tat_ca_da') ?></button></form>
    <?php endif; ?>
</div>

<nav class="tabs" aria-label="<?= te('notification.index.loc_thong_bao') ?>">
    <a href="<?= e(app_url('/notifications')) ?>" aria-selected="<?= $filter === 'all' ? 'true' : 'false' ?>"><?= te('common.all') ?></a>
    <a href="<?= e(app_url('/notifications?filter=unread')) ?>" aria-selected="<?= $filter === 'unread' ? 'true' : 'false' ?>">Chưa đọc<?php if ($unread): ?><span class="count"><?= fmt_int($unread) ?></span><?php endif; ?></a>
</nav>

<?php if (!$notifications): ?>
    <div class="empty"><p><?= $filter === 'unread' ? te('notification.index.khong_co_thong_bao_chua') : te('notification.index.khi_co_don_hang_danh') ?></p></div>
<?php else: ?>
<section class="panel">
    <div class="rows">
        <?php foreach ($notifications as $n): $isUnread = !(int)$n['da_doc']; ?>
            <div class="row note-row<?= $isUnread ? ' unread' : '' ?>">
                <span class="dot" aria-hidden="true"></span>
                <div class="grow">
                    <?php if ($n['url_dich']): ?>
                        <form method="post" action="<?= e(app_url('/notifications/' . (int)$n['ma'] . '/read')) ?>" class="note-open">
                            <?= csrf_field() ?><input type="hidden" name="open" value="1">
                            <button type="submit"><?= e($n['tieu_de']) ?><?= $isUnread ? '<span class="sr-only"> (' . te('notification.index.chua_doc') . ')</span>' : '' ?></button>
                        </form>
                    <?php else: ?>
                        <strong><?= e($n['tieu_de']) ?></strong>
                    <?php endif; ?>
                    <?php if ($n['noi_dung_thong_bao']): ?><span class="sub"><?= e(excerpt($n['noi_dung_thong_bao'], 200)) ?></span><?php endif; ?>
                </div>
                <span class="small faint" title="<?= e(fmt_datetime($n['ngay_tao'])) ?>"><?= e(time_ago($n['ngay_tao'])) ?></span>
                <?php if ($isUnread): ?>
                    <form method="post" action="<?= e(app_url('/notifications/' . (int)$n['ma'] . '/read')) ?>"><?= csrf_field() ?><?= return_field() ?><button class="link-btn" type="submit"><?= te('notification.index.da_doc') ?></button></form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php partial('pagination', ['page' => $page, 'pages' => $pages, 'total' => $total]); ?>
</section>
<?php endif; ?>
