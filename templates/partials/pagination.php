<?php
/** @var int $page @var int $pages @var int $total */
if (($pages ?? 1) <= 1) return;
?>
<nav class="pagination" aria-label="<?= te('partials.pagination.phan_trang') ?>">
    <span><?= te('partials.pagination.trang-muc', [ fmt_int($page) ,  fmt_int($pages) ,  fmt_int($total) ]) ?></span>
    <span class="pages">
        <?php if ($page > 1): ?><a class="btn sm" href="<?= e(qs(['page' => $page - 1])) ?>"><?= te('partials.pagination.trang_truoc') ?></a><?php endif; ?>
        <?php if ($page < $pages): ?><a class="btn sm" href="<?= e(qs(['page' => $page + 1])) ?>">Trang sau</a><?php endif; ?>
    </span>
</nav>
