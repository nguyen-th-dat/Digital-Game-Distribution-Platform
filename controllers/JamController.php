<?php
// =============================================================================
// JAM CONTROLLER - public side of game jams: list, detail, submit, withdraw,
// vote. Jams are created and run from /admin/jams (JamAdminController).
// =============================================================================

class JamController extends Controller {
    private const TABS = ['open', 'judging', 'past'];

    public function index(array $params = []) {
        $tabs = ['open' => t('jam.index.dang.mo'), 'judging' => t('jam.index.dang.cham'), 'past' => t('jam.index.da.ket.thuc')];
        if (is_staff()) {
            $tabs['hidden'] = t('jam.index.nhap.va.da.huy');
        }
        $tab = (string)($_GET['tab'] ?? 'open');
        if (!isset($tabs[$tab])) {
            $tab = 'open';
        }
        [$jams, $pager] = $this->paginate(JamModel::listing($tab), 12);
        $this->render('jam/index', [
            'pageTitle' => t('jam.index.cuoc_thi_lam_game'),
            'tabs' => $tabs,
            'tab' => $tab,
            'counts' => JamModel::tabCounts(),
            'jams' => $jams,
        ] + $pager);
    }

    public function show(array $params = []) {
        $jam = $this->visibleJam($params);
        $jid = (int)$jam['ma'];
        $uid = current_user_id();
        $phase = JamModel::phase($jam);
        $manager = $this->canManage($jam);
        $entries = JamModel::entries($jid, $manager);
        $myVotes = JamModel::myVotes($jid, $uid);
        $authored = [];
        foreach ($entries as $en) {
            if ($uid && GameModel::isAuthor((int)$en['ma'], $uid)) {
                $authored[(int)$en['entry_id']] = true;
            }
        }
        if ($phase === 'results') {
            usort($entries, static fn($a, $b) => [(float)$b['diem_tb'], (int)$b['so_phieu']] <=> [(float)$a['diem_tb'], (int)$a['so_phieu']]);
        } elseif ($phase === 'voting') {
            // Stable per-viewer order so early entries do not collect every vote.
            $seed = (string)($uid ?: session_id());
            usort($entries, static fn($a, $b) => crc32($seed . ':' . $a['entry_id']) <=> crc32($seed . ':' . $b['entry_id']));
        }
        $this->render('jam/show', [
            'pageTitle' => $jam['tieu_de'],
            'jam' => $jam,
            'phase' => $phase,
            'entries' => $entries,
            'scores' => $phase === 'results' || $manager ? JamModel::criteriaScores($jid) : [],
            'myVotes' => $myVotes,
            'authored' => $authored,
            'members' => JamModel::members($jid),
            'eligible' => $uid && $phase === 'submissions' ? JamModel::eligibleGames($jid, $uid) : [],
            'board' => Model::one('SELECT ma, tieu_de FROM dien_dan WHERE cuoc_thi_id = :j' . (is_staff() ? '' : ' AND cong_khai = 1'), [':j' => $jid]),
            'manager' => $manager,
        ]);
    }

    public function submit(array $params = []) {
        $jam = $this->visibleJam($params);
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $self = '/jams/' . rawurlencode($jam['duong_dan_rut_gon']);
        if (JamModel::phase($jam) !== 'submissions') {
            $this->fail(t('jam.cuoc.thi.hien.khong.nhan.bai'), $self);
        }
        $gameId = (int)($_POST['tro_choi_id'] ?? 0);
        $game = Model::one('SELECT ma, tieu_de, trang_thai FROM tro_choi WHERE ma = :g', [':g' => $gameId]);
        if (!$game || !GameModel::isAuthor($gameId, $uid)) {
            $this->fail(t('jam.ban.chi.nop.duoc.tro.choi.do.minh.phat.trien'), $self . '#submit');
        }
        if (!in_array($game['trang_thai'], ['published', 'unlisted'], true)) {
            $this->fail(t('jam.tro.choi.can.o.trang.thai.cong.bo.hoac.khong.liet.ke.thi.nguoi.cham.moi.mo.duoc'), $self . '#submit');
        }
        $existing = Model::scalar('SELECT trang_thai FROM bai_du_thi WHERE cuoc_thi_id = :j AND tro_choi_id = :g', [':j' => (int)$jam['ma'], ':g' => $gameId]);
        if ($existing === 'submitted') {
            $this->fail(t('jam.tro.choi.nay.da.co.trong.cuoc.thi'), $self . '#entries');
        }
        if ($existing === 'disqualified') {
            $this->fail(t('jam.tro.choi.nay.da.bi.loai.khoi.cuoc.thi.va.khong.nop.lai.duoc'), $self . '#submit');
        }
        $entryId = JamModel::submit((int)$jam['ma'], $gameId, $uid);
        audit('jam_submit', 'bai_du_thi', $entryId, ['jam' => (int)$jam['ma'], 'game' => $gameId]);
        $hosts = array_map('intval', Model::column("SELECT nguoi_dung_id FROM thanh_vien_cuoc_thi WHERE cuoc_thi_id = :j AND vai_tro_thanh_vien IN ('host','admin')", [':j' => (int)$jam['ma']]));
        NotificationModel::notifyUsers($hosts, $uid, 'jam_entry',t('jam.bai.du.thi.moi', [$game['tieu_de']]),t('jam.nop.vao', [$jam['tieu_de']]), $self . '#entry-' . $entryId);
        flash('success',t('jam.da.nop.vao.cuoc.thi', [$game['tieu_de']]));
        $this->redirect($self . '#entry-' . $entryId);
    }

    public function withdraw(array $params = []) {
        $jam = $this->visibleJam($params);
        $user = $this->requireAuth();
        $self = '/jams/' . rawurlencode($jam['duong_dan_rut_gon']);
        $entry = JamModel::entry((int)$jam['ma'], (int)($params['entry'] ?? 0));
        if (!$entry || $entry['trang_thai'] !== 'submitted') {
            $this->notFound(t('jam.khong.tim.thay.bai.du.thi.nay'));
        }
        if (!GameModel::isAuthor((int)$entry['tro_choi_id'], (int)$user['ma'])) {
            $this->forbidden(t('jam.chi.nha.phat.trien.cua.tro.choi.moi.rut.duoc.bai'));
        }
        if (JamModel::phase($jam) !== 'submissions') {
            $this->fail(t('jam.da.het.han.nop.bai.nen.khong.rut.bai.duoc.nua'), $self . '#entry-' . (int)$entry['ma']);
        }
        Model::run("UPDATE bai_du_thi SET trang_thai = 'withdrawn' WHERE ma = :e", [':e' => (int)$entry['ma']]);
        audit('jam_withdraw', 'bai_du_thi', (int)$entry['ma']);
        flash('success',t('jam.da.rut.khoi.cuoc.thi.ban.co.the.nop.lai.truoc.han', [$entry['game_title']]));
        $this->redirect($self . '#submit');
    }

    public function vote(array $params = []) {
        $jam = $this->visibleJam($params);
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $self = '/jams/' . rawurlencode($jam['duong_dan_rut_gon']);
        $entry = JamModel::entry((int)$jam['ma'], (int)($params['entry'] ?? 0));
        if (!$entry || $entry['trang_thai'] !== 'submitted' || !in_array($entry['game_status'], ['published', 'unlisted'], true)) {
            $this->notFound(t('jam.khong.tim.thay.bai.du.thi.nay'));
        }
        $anchor = $self . '#entry-' . (int)$entry['ma'];
        if (JamModel::phase($jam) !== 'voting') {
            $this->fail(t('jam.cuoc.thi.hien.khong.mo.binh.chon'), $anchor);
        }
        if (GameModel::isAuthor((int)$entry['tro_choi_id'], $uid)) {
            $this->fail(t('jam.ban.khong.cham.diem.tro.choi.cua.chinh.minh'), $anchor);
        }
        $scores = [];
        foreach (array_keys(JamModel::CRITERIA) as $criterion) {
            $value = (int)($_POST['diem'][$criterion] ?? 0);
            if ($value < 1 || $value > 5) {
                $this->fail(t('jam.hay.cham.tu.1.den.5.cho.moi.tieu.chi'), $anchor);
            }
            $scores[$criterion] = $value;
        }
        $comment = post_str('binh_luan', 1000);
        if ($comment === null) {
            $this->fail(t('jam.nhan.xet.toi.da.1.000.ky.tu'), $anchor);
        }
        JamModel::vote((int)$entry['ma'], $uid, $scores, $comment !== '' ? $comment : null);
        audit('jam_vote', 'bai_du_thi', (int)$entry['ma']);
        flash('success',t('jam.da.luu.diem.cho', [$entry['game_title']]));
        $this->redirect($anchor);
    }

    // ------------------------------------------------------------------ helpers

    private function canManage(array $jam): bool {
        $uid = current_user_id();
        if (!$uid) {
            return false;
        }
        return is_staff() || in_array(JamModel::memberRole((int)$jam['ma'], $uid), ['host', 'admin'], true);
    }

    /** Drafts are only visible to staff and the jam's own team; cancelled jams stay readable. */
    private function visibleJam(array $params): array {
        $jam = JamModel::bySlug((string)($params['slug'] ?? ''));
        if (!$jam) {
            $this->notFound(t('jam.khong.tim.thay.cuoc.thi.nay'));
        }
        if ($jam['trang_thai'] === 'draft' && !is_staff() && !JamModel::memberRole((int)$jam['ma'], current_user_id())) {
            $this->notFound(t('jam.khong.tim.thay.cuoc.thi.nay'));
        }
        return $jam;
    }
}
