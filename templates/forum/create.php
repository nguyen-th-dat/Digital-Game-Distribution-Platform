<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section class="card forum-create">
    <p><a href="<?php echo app_url('/forums'); ?>">← Diễn đàn</a></p>
    <h2>Tạo diễn đàn</h2>
    <p>Tạo một không gian để mọi người cùng trao đổi và chia sẻ.</p>
    <form method="post" action="<?php echo app_url('/forums'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $escape($csrfToken); ?>">
        <label for="forum-title">Tên diễn đàn</label>
        <input id="forum-title" name="tieu_de" maxlength="180" required style="width:100%">
        <label for="forum-description">Mô tả</label>
        <textarea id="forum-description" name="mo_ta" maxlength="1000" rows="6" style="width:100%"></textarea>
        <p><button class="button" type="submit">Tạo diễn đàn</button></p>
    </form>
</section>
<style>
    .forum-create { max-width: 720px; margin: auto; }
    .forum-create h2 { margin-bottom: 8px; }
</style>
