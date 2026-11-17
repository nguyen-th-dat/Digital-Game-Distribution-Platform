<?php
/** New topic form. @var array $board */
$id = (int)$board['ma'];
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url('/forums')) ?>"><?= te('forum.forums') ?></a><a href="<?= e(app_url('/forums/' . $id)) ?>"><?= e($board['tieu_de']) ?></a><span><?= te('forum.new_topic') ?></span></nav>

<div class="title-row"><div><h1><?= te('forum.new_topic') ?></h1><p class="sub">Đăng trong <?= e($board['tieu_de']) ?>. Tiêu đề rõ ràng giúp người khác tìm thấy và trả lời nhanh hơn.</p></div></div>

<section class="panel narrow-panel">
    <form class="form" method="post" action="<?= e(app_url('/forums/' . $id . '/topics')) ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label for="t-title"><?= te('common.title') ?></label>
            <input class="input" id="t-title" name="tieu_de" minlength="3" maxlength="220" required value="<?= e(old('tieu_de')) ?>">
        </div>
        <div class="field">
            <label for="t-body"><?= te('admin.comments') ?></label>
            <textarea class="input tall" id="t-body" name="noi_dung" maxlength="20000" required><?= e(old('noi_dung')) ?></textarea>
            <span class="hint"><?= te('forum.new-topic.van_ban_thuan_xuong_dong') ?></span>
        </div>
        <div class="form-actions">
            <button class="btn primary" type="submit"><?= te('forum.new-topic.dang_chu_de') ?></button>
            <a class="btn ghost" href="<?= e(app_url('/forums/' . $id)) ?>"><?= te('common.cancel') ?></a>
        </div>
    </form>
</section>
