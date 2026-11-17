<?php
/** Report detail. @var array $report @var ?array $target @var array $options @var array $related @var array $history */
$rid = (int)$report['ma'];
$type = $report['loai_doi_tuong'];
$tid = (int)$report['doi_tuong_id'];
$isOpen = in_array($report['trang_thai'], ['open', 'investigating'], true);
$openRelated = count(array_filter($related, static fn($r) => in_array($r['trang_thai'], ['open', 'investigating'], true)));
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url('/admin/reports')) ?>"><?= te('admin.reports') ?></a><span><?= te('admin.report.bao-cao', [ $rid ]) ?></span></nav>

<div class="title-row">
    <div><h1><?= e(label('report_target', $type)) ?> #<?= $tid ?></h1><p class="sub"><?= te('admin.report.gui', [ e(label('report_reason', $report['ma_ly_do'])) ,  e(fmt_datetime($report['ngay_tao'])) ,  status_pill('report_status', $report['trang_thai']) ]) ?></p></div>
    <?php if ($target): ?><a class="btn" href="<?= e(app_url(mod_target_url($type, $tid))) ?>"><?= te('admin.report.mo_trong_trang_quan_ly') ?></a><?php endif; ?>
</div>

<div class="grid top section">
    <div class="span-7 stack">
        <section class="panel" aria-labelledby="rep-h">
            <header><h2 id="rep-h"><?= te('admin.report.noi_dung_bao_cao') ?></h2></header>
            <dl class="facts">
                <dt><?= te('admin.reporter') ?></dt><dd><?= $report['nguoi_bao_cao'] ? e($report['nguoi_bao_cao']) . ' <span class="muted">@' . e($report['nguoi_bao_cao_dn']) . '</span>' : te('admin.dashboard.tai_khoan_da_xoa') ?></dd>
                <dt><?= te('admin.reason') ?></dt><dd><?= e(label('report_reason', $report['ma_ly_do'])) ?></dd>
                <dt><?= te('admin.report.nguoi_xu_ly') ?></dt><dd><?= $report['nguoi_xu_ly'] ? e($report['nguoi_xu_ly']) : te('dev.game-keys.chua_co') ?></dd>
                <?php if ($report['ngay_giai_quyet']): ?><dt><?= te('admin.report.dong_luc') ?></dt><dd><?= e(fmt_datetime($report['ngay_giai_quyet'])) ?></dd><?php endif; ?>
            </dl>
            <?php if ((string)$report['chi_tiet'] !== ''): ?><div class="well"><?= nl2br(e($report['chi_tiet'])) ?></div><?php else: ?><p class="muted"><?= te('admin.report.nguoi_bao_cao_khong_ghi') ?></p><?php endif; ?>
        </section>

        <section class="panel" aria-labelledby="target-h">
            <header><h2 id="target-h"><?= te('admin.report.doi_tuong_bi_bao_cao') ?></h2><?php if ($target): ?><span class="meta"><?= status_pill($type === 'game' ? 'game_status' : ($type === 'user' ? 'user_status' : 'content_status'), $target['trang_thai']) ?></span><?php endif; ?></header>
            <?php if (!$target): ?>
                <p class="muted"><?= te('admin.report.doi_tuong_nay_khong_con') ?></p>
            <?php elseif ($type === 'game'): ?>
                <dl class="facts">
                    <dt><?= te('admin.games') ?></dt><dd><?php if (in_array($target['trang_thai'], ['published', 'unlisted'], true)): ?><a class="link" href="<?= e(game_url($target)) ?>"><?= e($target['tieu_de']) ?></a><?php else: ?><?= e($target['tieu_de']) ?><?php endif; ?></dd>
                    <dt><?= te('admin.report.nguoi_tao') ?></dt><dd><?= e($target['ten_hien_thi']) ?> <span class="muted">@<?= e($target['ten_dang_nhap']) ?></span></dd>
                </dl>
                <?php if ((string)$target['mo_ta_ngan'] !== ''): ?><div class="well"><?= e($target['mo_ta_ngan']) ?></div><?php endif; ?>
            <?php elseif ($type === 'user'): ?>
                <div class="row">
                    <img class="avatar lg" src="<?= e(avatar_url($target)) ?>" alt="">
                    <div class="grow"><?php if ($target['trang_thai'] === 'active'): ?><a class="link" href="<?= e(profile_url($target)) ?>"><?= e($target['ten_hien_thi']) ?></a><?php else: ?><?= e($target['ten_hien_thi']) ?><?php endif; ?><span class="sub">@<?= e($target['ten_dang_nhap']) ?></span></div>
                </div>
                <?php if ((string)$target['gioi_thieu'] !== ''): ?><div class="well"><?= nl2br(e($target['gioi_thieu'])) ?></div><?php endif; ?>
            <?php else: ?>
                <dl class="facts">
                    <dt><?= te('common.author') ?></dt><dd><?= e($target['ten_hien_thi']) ?> <span class="muted">@<?= e($target['ten_dang_nhap']) ?></span></dd>
                    <dt><?= te('admin.report.thuoc') ?></dt><dd><?= e($target['ngu_canh']) ?></dd>
                    <dt><?= te('admin.report.dang_luc') ?></dt><dd><?= e(fmt_datetime($target['ngay_tao'])) ?></dd>
                </dl>
                <div class="well">
                    <?php if ((string)$target['tieu_de'] !== ''): ?><strong><?= e($target['tieu_de']) ?></strong><br><?php endif; ?>
                    <?= nl2br(e(excerpt((string)$target['noi_dung'], 1200))) ?>
                </div>
                <?php if ($target['trang_thai'] === 'published'): ?><p><a class="link" href="<?= e(app_url($target['url'])) ?>"><?= te('admin.content.xem_tai_trang_goc') ?></a></p><?php endif; ?>
            <?php endif; ?>
        </section>

        <section class="panel" aria-labelledby="hist-h">
            <header><h2 id="hist-h"><?= te('admin.report.lich_su_kiem_duyet_cua') ?></h2></header>
            <?php if (!$history): ?><p class="muted"><?= te('admin.report.chua_co_thao_tac_kiem') ?></p><?php endif; ?>
            <div class="rows">
                <?php foreach ($history as $h): ?>
                    <div class="row"><div class="grow"><?= e(label('mod_action', $h['loai_hanh_dong'])) ?><span class="sub"><?= e($h['ten_hien_thi']) ?> · <?= e(fmt_datetime($h['ngay_tao'])) ?><?= (string)$h['ly_do'] !== '' ? ' · ' . e($h['ly_do']) : '' ?></span></div></div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="span-5 stack">
        <?php if ($isOpen): ?>
            <?php if ($report['trang_thai'] === 'open'): ?>
            <section class="panel" aria-labelledby="claim-h">
                <header><h2 id="claim-h"><?= te('admin.report.nhan_xu_ly') ?></h2></header>
                <p class="muted"><?= te('admin.report.danh_dau_bao_cao_dang') ?></p>
                <form class="form" method="post" action="<?= e(app_url('/admin/reports/' . $rid)) ?>">
                    <?= csrf_field() ?><input type="hidden" name="do" value="investigate">
                    <div class="form-actions"><button class="btn" type="submit"><?= te('admin.report.toi_se_xu_ly') ?></button></div>
                </form>
            </section>
            <?php endif; ?>

            <section class="panel" aria-labelledby="close-h">
                <header><h2 id="close-h"><?= te('admin.report.dong_bao_cao') ?></h2></header>
                <form class="form" method="post" action="<?= e(app_url('/admin/reports/' . $rid)) ?>">
                    <?= csrf_field() ?>
                    <fieldset class="field">
                        <legend><?= te('admin.report.ket_qua') ?></legend>
                        <label class="check"><input type="radio" name="ket_qua" value="resolved" required<?= old('ket_qua') === 'resolved' ? ' checked' : '' ?>> <span><?= te('admin.report.vi_pham_da_xu_ly') ?></span></label>
                        <label class="check"><input type="radio" name="ket_qua" value="rejected"<?= old('ket_qua') === 'rejected' ? ' checked' : '' ?>> <span><?= te('admin.report.khong_vi_pham_bac_bo') ?></span></label>
                    </fieldset>
                    <?php if ($options): ?>
                    <div class="field"><label for="r-apply"><?= te('admin.report.bien_phap') ?></label>
                        <select class="input" id="r-apply" name="xu_ly">
                            <option value=""><?= te('admin.report.khong_ap_dung_them') ?></option>
                            <?php foreach ($options as $k => $t): ?><option value="<?= e($k) ?>"<?= old('xu_ly') === $k ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
                        </select>
                        <span class="hint"><?= te('admin.report.chi_dung_khi_ket_qua') ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="field"><label for="r-note"><?= te('common.note') ?></label><textarea class="input" id="r-note" name="ghi_chu" maxlength="1000"><?= e(old('ghi_chu')) ?></textarea><span class="hint"><?= te('admin.report.dung_lam_ly_do_cua') ?></span></div>
                    <?php if ($openRelated > 0): ?>
                        <label class="check"><input type="checkbox" name="dong_cung_noi_dung" value="1" checked> <span><?= te('admin.report.dong-luon-bao-cao-khac-cung-noi-dung', [ fmt_int($openRelated) ]) ?></span></label>
                    <?php endif; ?>
                    <div class="form-actions"><button class="btn primary" type="submit"><?= te('admin.report.dong_bao_cao') ?></button></div>
                </form>
            </section>
        <?php else: ?>
            <section class="panel"><header><h2><?= te('admin.report.da_dong') ?></h2></header><p class="muted"><?= te('admin.report.bao-cao-nay-da-dong-voi-ket-qua', [ e(label('report_status', $report['trang_thai'])) ]) ?></p></section>
        <?php endif; ?>

        <section class="panel" aria-labelledby="rel-h">
            <header><h2 id="rel-h"><?= te('admin.report.bao_cao_khac_cung_noi') ?></h2><span class="meta"><?= fmt_int(count($related)) ?></span></header>
            <?php if (!$related): ?><p class="muted"><?= te('admin.report.chi_co_bao_cao_nay') ?></p><?php endif; ?>
            <div class="rows">
                <?php foreach ($related as $o): ?>
                    <div class="row">
                        <div class="grow"><a class="link" href="<?= e(app_url('/admin/reports/' . (int)$o['ma'])) ?>">#<?= (int)$o['ma'] ?> · <?= e(label('report_reason', $o['ma_ly_do'])) ?></a><span class="sub"><?= e($o['ten_hien_thi'] ?? te('admin.dashboard.tai_khoan_da_xoa')) ?> · <?= e(fmt_datetime($o['ngay_tao'])) ?></span></div>
                        <?= status_pill('report_status', $o['trang_thai']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>
