<?php
/** Genres, tags and platforms. @var string $kindKey @var array $kind @var array $items @var array $counts @var ?array $editing */
$base = '/admin/taxonomy';
$isPlatform = $kindKey === 'platform';
$keyLabel = $isPlatform ? te('admin.taxonomy.ma_nen_tang') : te('dev.game_slug');
$editId = $editing ? (int)$editing['ma'] : 0;
$val = static fn(string $field, ?string $col) => old($field, $editing && $col ? (string)($editing[$col] ?? '') : '');
?>
<div class="title-row">
    <div><h1><?= te('admin.taxonomy') ?></h1><p class="sub"><?= te('admin.taxonomy.the_loai_the_va_nen') ?></p></div>
</div>

<nav class="tabs" aria-label="<?= te('admin.taxonomy.loai_danh_muc') ?>">
    <?php foreach (TaxonomyModel::kinds() as $key => $k): ?>
        <a href="<?= e(app_url($base . '?kind=' . $key)) ?>" aria-selected="<?= $key === $kindKey ? 'true' : 'false' ?>"><?= e($k['label']) ?> <span class="muted"><?= fmt_int($counts[$key]) ?></span></a>
    <?php endforeach; ?>
</nav>

<div class="grid top">
    <section class="panel span-8" aria-labelledby="list-h">
        <header><h2 id="list-h"><?= e($kind['label']) ?></h2><span class="meta"><?= te('admin.taxonomy.muc', [ fmt_int(count($items)) ]) ?></span></header>
        <?php if (!$items): ?>
            <div class="empty"><p><?= te('admin.taxonomy.chua-co-nao', [ e($kind['one']) ]) ?></p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th scope="col"><?= te('common.name') ?></th><?php if ($kind['desc']): ?><th scope="col"><?= te('common.description') ?></th><?php endif; ?>
                    <th scope="col" class="num"><?= te('admin.games') ?></th><th scope="col"><span class="sr-only"><?= te('common.actions') ?></span></th>
                </tr></thead>
                <tbody>
                <?php foreach ($items as $it): $id = (int)$it['ma']; $n = (int)$it['so_tro_choi']; ?>
                    <tr<?= $id === $editId ? ' aria-current="true"' : '' ?>>
                        <td><?= e($it['ten_muc']) ?><span class="sub"><code><?= e($it['khoa_muc']) ?></code></span></td>
                        <?php if ($kind['desc']): ?><td class="muted"><?= $it[$kind['desc']] !== null ? e($it[$kind['desc']]) : '' ?></td><?php endif; ?>
                        <td class="num"><?php if ($n): ?><a class="link" href="<?= e(app_url('/games?' . $kind['filter'] . '=' . rawurlencode($it['khoa_muc']))) ?>"><?= fmt_int($n) ?></a><?php else: ?><span class="muted">0</span><?php endif; ?></td>
                        <td class="actions">
                            <a class="link" href="<?= e(app_url($base . '?kind=' . $kindKey . '&edit=' . $id . '#form')) ?>"><?= te('common.edit') ?><span class="sr-only"> <?= e($it['ten_muc']) ?></span></a>
                            <?php if (!$n): ?>
                                <form method="post" action="<?= e(app_url($base . '/' . $kindKey . '/' . $id . '/delete')) ?>" data-confirm="<?= te('admin.taxonomy.xoa-e-kind-one-quot-e-it-ten-muc-quot') ?>">
                                    <?= csrf_field() ?>
                                    <button class="link-btn neg" type="submit"><?= te('admin.delete') ?><span class="sr-only"> <?= e($it['ten_muc']) ?></span></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <section class="panel span-4" id="form" aria-labelledby="form-h">
        <header><h2 id="form-h"><?= $editing ? te('common.edit') . ' ' . e($kind['one']) : te('common.add') . ' ' . e($kind['one']) ?></h2></header>
        <form class="form" method="post" action="<?= e(app_url($base . '/' . $kindKey . ($editing ? '/' . $editId : ''))) ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="tx-name"><?= te('common.name') ?></label>
                <input class="input" id="tx-name" name="ten" required maxlength="<?= (int)$kind['nameMax'] ?>" value="<?= e($val('ten', $kind['name'])) ?>">
            </div>
            <div class="field">
                <label for="tx-key"><?= e($keyLabel) ?></label>
                <input class="input" id="tx-key" name="khoa" maxlength="<?= (int)$kind['slugMax'] ?>" pattern="[a-z0-9]+(-[a-z0-9]+)*" autocomplete="off" spellcheck="false" value="<?= e($val('khoa', $kind['slug'])) ?>">
                <span class="hint"><?= te('admin.taxonomy.chu-thuong-khong-dau-so-gach-ngang-bo-trong-de-tao-tu-ten-dung-trong-dia-chi-trang-loc-cua-cua-hang', [ $editing ? '; ' . te('admin.taxonomy.doi_gia_tri_nay_lam') : '' ]) ?></span>
            </div>
            <?php if ($kind['desc']): ?>
            <div class="field">
                <label for="tx-desc"><?= te('common.description') ?></label>
                <textarea class="input" id="tx-desc" name="mo_ta" maxlength="<?= (int)$kind['descMax'] ?>" rows="3"><?= e($val('mo_ta', $kind['desc'])) ?></textarea>
            </div>
            <?php endif; ?>
            <div class="form-actions">
                <button class="btn primary" type="submit"><?= $editing ? te('collection.show.luu_thay_doi') : te('common.add') ?></button>
                <?php if ($editing): ?><a class="btn" href="<?= e(app_url($base . '?kind=' . $kindKey)) ?>"><?= te('common.cancel') ?></a><?php endif; ?>
            </div>
        </form>
    </section>
</div>
