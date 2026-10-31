<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section class="card topic-create">
    <p><a href="<?php echo app_url('/forums/' . (int)$forum['ma']); ?>">← <?php echo $escape($forum['tieu_de']); ?></a></p>
    <h2>Tạo chủ đề</h2>
    <form method="post" action="<?php echo app_url('/forums/' . (int)$forum['ma'] . '/topics'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $escape($csrfToken); ?>">
        <label for="topic-title">Tiêu đề</label>
        <input id="topic-title" name="tieu_de" maxlength="220" required style="width:100%">
        <label for="topic-content">Nội dung</label>
        <textarea id="topic-content" name="noi_dung" maxlength="10000" rows="8" required style="width:100%"></textarea>
        <p><button class="button" type="submit">Đăng chủ đề</button></p>
    </form>
</section>
<style>
    .topic-create { max-width: 760px; margin: auto; }
    .topic-create h2 { margin-bottom: 12px; }
</style>
