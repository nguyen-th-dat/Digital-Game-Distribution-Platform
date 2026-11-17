<?php
/**
 * Jam detail.
 * @var array $jam @var string $phase @var array $entries @var array $scores @var array $myVotes @var array $authored
 * @var array $members @var array $eligible @var ?array $board @var bool $manager
 */
$user = current_user();
$slug = rawurlencode($jam['duong_dan_rut_gon']);
$self = '/jams/' . $slug;
$voting = $phase === 'voting';
$results = $phase === 'results';
$pillClass = in_array($phase, ['submissions', 'voting'], true) ? ' pos' : ($phase === 'cancelled' ? ' neg' : '');
$voted = count(array_filter($myVotes, static fn($v) => isset($v['scores']['overall'])));
?>
<nav class="crumbs" aria-label="<?= te('admin.campaign-form.duong_dan') ?>"><a href="<?= e(app_url('/jams')) ?>"><?= te('admin.jams.cuoc_thi') ?></a><span><?= e($jam['tieu_de']) ?></span></nav>

<header class="object-head">
    <div class="grow">
        <h1><?= e($jam['tieu_de']) ?></h1>
        <p class="meta"><?= te('jam.show.to_chuc_boi') ?> <a class="link" href="<?= e(profile_url($jam)) ?>"><?= e($jam['ten_hien_thi']) ?></a> <?= te('admin.payouts.text') ?> <span class="pill<?= $pillClass ?>"><?= e(JamModel::phaseName($phase)) ?></span></p>
        <div class="stat-line">
            <span><strong><?= fmt_int(count(array_filter($entries, static fn($en) => $en['entry_status'] === 'submitted'))) ?></strong> <?= te('jam.show.bai_du_thi') ?></span>
            <?php if ((int)$jam['bat_binh_chon']): ?><span><?= te('jam.show.co_binh_chon_cong_khai') ?></span><?php endif; ?>
            <?php if ($user && $voting): ?><span><strong><?= fmt_int($voted) ?></strong> <?= te('jam.show.bai_ban_da_cham') ?></span><?php endif; ?>
        </div>
    </div>
    <div class="actions">
        <?php if ($phase === 'submissions' && $user): ?><a class="btn primary" href="#submit"><?= te('jam.submit') ?></a><?php endif; ?>
        <?php if ($board): ?><a class="btn" href="<?= e(app_url('/forums/' . (int)$board['ma'])) ?>"><?= te('jam.show.dien_dan_cuoc_thi') ?></a><?php endif; ?>
        <?php if ($manager && is_staff()): ?><a class="btn ghost" href="<?= e(app_url('/admin/jams/' . (int)$jam['ma'])) ?>"><?= te('jam.show.quan_ly') ?></a><?php endif; ?>
    </div>
</header>

<?php if ($phase === 'cancelled'): ?><div class="notice section"><?= te('jam.show.cuoc_thi_nay_da_bi') ?></div><?php endif; ?>
<?php if ($phase === 'draft'): ?><div class="notice section"><?= te('jam.show.ban_nhap_chi_dieu_hanh') ?></div><?php endif; ?>

<div class="jam-layout section">
    <div class="jam-main">
        <?php if ($jam['mo_ta']): ?>
        <section class="panel" aria-labelledby="about-h">
            <header><h2 id="about-h"><?= te('game.about') ?></h2></header>
            <div class="prose"><?= e_multiline($jam['mo_ta']) ?></div>
        </section>
        <?php endif; ?>
        <?php if ($jam['the_le']): ?>
        <section class="panel" aria-labelledby="rules-h">
            <header><h2 id="rules-h"><?= te('admin.jam-form.the_le') ?></h2></header>
            <div class="prose"><?= e_multiline($jam['the_le']) ?></div>
        </section>
        <?php endif; ?>
    </div>

    <aside class="jam-side">
        <section class="panel" aria-labelledby="time-h">
            <header><h2 id="time-h"><?= te('jam.show.lich_trinh') ?></h2></header>
            <dl class="facts">
                <dt><?= te('admin.campaign-form.bat_dau') ?></dt><dd><?= e(fmt_datetime($jam['ngay_bat_dau'])) ?></dd>
                <dt><?= te('admin.jams.han_nop') ?></dt><dd><?= e(fmt_datetime($jam['han_nop_bai'])) ?></dd>
                <?php if ((int)$jam['bat_binh_chon']): ?>
                    <dt><?= te('jam.show.mo_binh_chon') ?></dt><dd><?= e(fmt_datetime($jam['ngay_bat_dau_binh_chon'] ?: $jam['han_nop_bai'])) ?></dd>
                    <dt><?= te('jam.show.dong_binh_chon') ?></dt><dd><?= $jam['ngay_ket_thuc_binh_chon'] ? e(fmt_datetime($jam['ngay_ket_thuc_binh_chon'])) : te('jam.show.khi_ban_to_chuc_cong') ?></dd>
                <?php endif; ?>
            </dl>
        </section>

        <?php if ($phase === 'submissions'): ?>
        <section class="panel" id="submit" aria-labelledby="submit-h">
            <header><h2 id="submit-h"><?= te('jam.submit') ?></h2></header>
            <?php if (!$user): ?>
                <p class="small muted"><?= te('jam.show.dang_nhap_de_nop_tro') ?></p>
                <a class="btn block" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($self . '#submit'))) ?>"><?= te('auth.login_button') ?></a>
            <?php elseif (!$eligible): ?>
                <p class="small muted"><?= te('jam.show.ban_chua_co_tro_choi') ?></p>
                <a class="btn block" href="<?= e(app_url('/dev/games/new')) ?>"><?= te('jam.show.tao_tro_choi') ?></a>
            <?php else: ?>
                <form class="form" method="post" action="<?= e(app_url($self . '/entries')) ?>">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label for="j-game"><?= te('admin.games') ?></label>
                        <select class="input" id="j-game" name="tro_choi_id" required>
                            <?php foreach ($eligible as $g): ?><option value="<?= (int)$g['ma'] ?>"><?= e($g['tieu_de']) ?></option><?php endforeach; ?>
                        </select>
                        <span class="hint"><?= te('jam.show.ban_co_the_rut_bai') ?></span>
                    </div>
                    <div class="form-actions"><button class="btn primary block" type="submit"><?= te('jam.show.nop_vao_cuoc_thi') ?></button></div>
                </form>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if ($members): ?>
        <section class="panel" aria-labelledby="team-h">
            <header><h2 id="team-h"><?= te('admin.jam-form.ban_to_chuc') ?></h2></header>
            <div class="rows">
                <?php foreach ($members as $m): ?>
                    <div class="row">
                        <img class="avatar" src="<?= e(avatar_url($m)) ?>" alt="" loading="lazy">
                        <div class="grow"><a class="link" href="<?= e(profile_url($m)) ?>"><?= e($m['ten_hien_thi']) ?></a><span class="sub"><?= e(label('jam_role', $m['vai_tro_thanh_vien'])) ?></span></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </aside>
</div>

<section class="section" id="entries" aria-labelledby="entries-h">
    <div class="section-head">
        <h2 id="entries-h"><?= $results ? te('admin.report.ket_qua') : te('jam.entries') ?></h2>
        <span class="meta"><?= $voting ? te('jam.show.thu_tu_hien_thi_duoc') : ($results ? te('jam.show.xep_theo_diem_tong_the') : fmt_int(count($entries)) . ' ' . te('jam.show.bai')) ?></span>
    </div>
    <?php if (!$entries): ?>
        <div class="empty"><p><?= $phase === 'submissions' ? te('jam.show.chua_co_bai_du_thi') : te('jam.show.cuoc_thi_chua_co_bai') ?></p></div>
    <?php elseif ($results): ?>
        <div class="panel">
            <table class="results">
                <thead><tr><th scope="col"><?= te('jam.show.hang') ?></th><th scope="col"><?= te('admin.games') ?></th><?php foreach (JamModel::criteria() as $c => $t): ?><th scope="col" class="num"><?= e($t) ?></th><?php endforeach; ?><th scope="col" class="num"><?= te('jam.show.luot_cham') ?></th></tr></thead>
                <tbody>
                <?php $rank = 0; foreach ($entries as $en): if ($en['entry_status'] !== 'submitted') continue; $rank++; $eid = (int)$en['entry_id']; ?>
                    <tr id="entry-<?= $eid ?>">
                        <td class="num"><?= (int)$en['so_phieu'] ? $rank : te('common.dash') ?></td>
                        <td><a class="link" href="<?= e(game_url($en)) ?>"><?= e($en['tieu_de']) ?></a><span class="sub"><?= e($en['nop_ten']) ?></span></td>
                        <?php foreach (array_keys(JamModel::CRITERIA) as $c): ?><td class="num"><?= isset($scores[$eid][$c]) ? number_format($scores[$eid][$c], 2, ',', '.') : te('common.dash') ?></td><?php endforeach; ?>
                        <td class="num"><?= fmt_int((int)$en['so_phieu']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="entry-grid">
            <?php foreach ($entries as $en):
                $eid = (int)$en['entry_id'];
                $mineEntry = !empty($authored[$eid]);
                $vote = $myVotes[$eid] ?? null;
                ?>
                <article class="entry" id="entry-<?= $eid ?>">
                    <?php partial('game-card', ['game' => $en]); ?>
                    <p class="small faint">Nộp bởi <?= e($en['nop_ten']) ?> · <?= e(fmt_date($en['ngay_nop'])) ?>
                        <?php if ($en['entry_status'] !== 'submitted'): ?> · <span class="pill warn"><?= e(['withdrawn' => te('label.entry_status.withdrawn'), 'disqualified' => te('label.entry_status.disqualified')][$en['entry_status']] ?? $en['entry_status']) ?></span><?php endif; ?>
                        <?php if ($manager && isset($scores[$eid]['overall'])): ?> · TB <?= number_format($scores[$eid]['overall'], 2, ',', '.') ?> (<?= fmt_int((int)$en['so_phieu']) ?> lượt)<?php endif; ?>
                    </p>
                    <?php if ($mineEntry && $phase === 'submissions' && $en['entry_status'] === 'submitted'): ?>
                        <form method="post" action="<?= e(app_url($self . '/entries/' . $eid . '/withdraw')) ?>" data-confirm="Rút <?= e($en['tieu_de']) ?> khỏi cuộc thi?">
                            <?= csrf_field() ?><button class="link-btn neg" type="submit"><?= te('jam.withdraw') ?></button>
                        </form>
                    <?php endif; ?>
                    <?php if ($voting && $en['entry_status'] === 'submitted'): ?>
                        <?php if (!$user): ?>
                            <a class="link small" href="<?= e(app_url('/auth/login?return_to=' . rawurlencode($self . '#entry-' . $eid))) ?>"><?= te('jam.show.dang_nhap_de_cham_diem') ?></a>
                        <?php elseif ($mineEntry): ?>
                            <p class="small faint"><?= te('jam.show.bai_cua_ban_khong_tu') ?></p>
                        <?php else: ?>
                            <details class="edit vote">
                                <summary class="btn sm<?= $vote ? '' : ' primary' ?>"><?= $vote ? te('jam.show.sua_diem_da_cham') . ' (' . (int)($vote['scores']['overall'] ?? 0) . '/5)' : te('jam.show.cham_diem') ?></summary>
                                <form class="form well vote-form" method="post" action="<?= e(app_url($self . '/entries/' . $eid . '/vote')) ?>">
                                    <?= csrf_field() ?>
                                    <?php foreach (JamModel::criteria() as $c => $t): $cur = (int)($vote['scores'][$c] ?? 0); ?>
                                        <fieldset class="score-row">
                                            <legend><?= e($t) ?></legend>
                                            <div class="score-options">
                                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                                    <label><input type="radio" name="diem[<?= e($c) ?>]" value="<?= $s ?>" required<?= $cur === $s ? ' checked' : '' ?>><span><?= $s ?></span></label>
                                                <?php endfor; ?>
                                            </div>
                                        </fieldset>
                                    <?php endforeach; ?>
                                    <div class="field">
                                        <label for="vc-<?= $eid ?>"><?= te('jam.show.nhan_xet_cho_nha_phat') ?></label>
                                        <textarea class="input" id="vc-<?= $eid ?>" name="binh_luan" maxlength="1000"><?= e((string)($vote['comment'] ?? '')) ?></textarea>
                                    </div>
                                    <div class="form-actions"><button class="btn sm primary" type="submit"><?= te('jam.show.luu_diem') ?></button></div>
                                </form>
                            </details>
                        <?php endif; ?>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
