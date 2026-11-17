<?php
// =============================================================================
// JAM ADMIN CONTROLLER - /admin/jams. Tables: cuoc_thi_lam_game,
// thanh_vien_cuoc_thi, bai_du_thi, dien_dan (optional jam board).
// =============================================================================

class JamAdminController extends Controller {
    public function index(array $params = []) {
        $this->requireRole('admin');
        $jams = Model::query("SELECT j.*, u.ten_hien_thi,
                (SELECT COUNT(*) FROM bai_du_thi e WHERE e.cuoc_thi_id = j.ma AND e.trang_thai = 'submitted') AS so_bai,
                (SELECT COUNT(DISTINCT v.nguoi_binh_chon_id) FROM binh_chon_bai_du_thi v JOIN bai_du_thi e ON e.ma = v.bai_du_thi_id WHERE e.cuoc_thi_id = j.ma) AS so_nguoi_cham
            FROM cuoc_thi_lam_game j JOIN nguoi_dung u ON u.ma = j.nguoi_tao_id ORDER BY j.ngay_bat_dau DESC, j.ma DESC");
        [$jams, $pager] = $this->paginate($jams, 20);
        $this->render('admin/jams', ['pageTitle' => t('jam.manage'), 'jams' => $jams] + $pager);
    }

    public function create(array $params = []) {
        $this->requireRole('admin');
        $this->render('admin/jam-form', ['pageTitle' => t('admin.jams.tao_cuoc_thi'), 'jam' => null, 'members' => [], 'entries' => [], 'board' => null]);
    }

    public function store(array $params = []) {
        $user = $this->requireRole('admin');
        $row = $this->validated(null, '/admin/jams/new');
        $id = JamModel::transaction(function () use ($row, $user) {
            $id = JamModel::insert($row + ['nguoi_tao_id' => (int)$user['ma']]);
            Model::run("INSERT INTO thanh_vien_cuoc_thi (cuoc_thi_id, nguoi_dung_id, vai_tro_thanh_vien) VALUES (:j, :u, 'host')", [':j' => $id, ':u' => (int)$user['ma']]);
            if (post_bool('tao_dien_dan')) {
                Model::run("INSERT INTO dien_dan (loai_dien_dan, cuoc_thi_id, tieu_de, mo_ta, cong_khai) VALUES ('jam', :j, :t, NULL, 1)", [':j' => $id, ':t' => mb_substr(t('forum.thao.luan', [$row['tieu_de']]), 0, 180)]);
            }
            return $id;
        });
        audit('jam_create', 'cuoc_thi_lam_game', $id);
        flash('success',t('jam.admin.da.tao.cuoc.thi', [$row['tieu_de']]));
        $this->redirect('/admin/jams/' . $id);
    }

    public function edit(array $params = []) {
        $this->requireRole('admin');
        $jam = $this->jam($params);
        $this->render('admin/jam-form', [
            'pageTitle' => t('bundle.admin.sua', [$jam['tieu_de']]),
            'jam' => $jam,
            'members' => JamModel::members((int)$jam['ma']),
            'entries' => JamModel::entries((int)$jam['ma'], true),
            'board' => Model::one('SELECT ma, tieu_de FROM dien_dan WHERE cuoc_thi_id = :j', [':j' => (int)$jam['ma']]),
        ]);
    }

    public function update(array $params = []) {
        $this->requireRole('admin');
        $jam = $this->jam($params);
        $id = (int)$jam['ma'];
        $row = $this->validated($jam, '/admin/jams/' . $id);
        JamModel::transaction(function () use ($row, $id) {
            JamModel::update($id, $row + ['ngay_cap_nhat' => date('Y-m-d H:i:s')]);
            if (post_bool('tao_dien_dan') && !Model::scalar('SELECT 1 FROM dien_dan WHERE cuoc_thi_id = :j', [':j' => $id])) {
                Model::run("INSERT INTO dien_dan (loai_dien_dan, cuoc_thi_id, tieu_de, cong_khai) VALUES ('jam', :j, :t, 1)", [':j' => $id, ':t' => mb_substr(t('forum.thao.luan', [$row['tieu_de']]), 0, 180)]);
            }
        });
        audit('jam_update', 'cuoc_thi_lam_game', $id, ['trang_thai' => $row['trang_thai']]);
        if ($row['trang_thai'] === 'completed' && $jam['trang_thai'] !== 'completed') {
            $entrants = array_map('intval', Model::column("SELECT DISTINCT nguoi_nop_id FROM bai_du_thi WHERE cuoc_thi_id = :j AND trang_thai = 'submitted'", [':j' => $id]));
            NotificationModel::notifyUsers($entrants, current_user_id(), 'jam_results',t('jam.admin.da.co.ket.qua', [$row['tieu_de']]),t('jam.admin.xem.thu.hang.va.diem.cua.bai.du.thi'), '/jams/' . rawurlencode($row['duong_dan_rut_gon']) . '#entries');
        }
        flash('success',t('jam.admin.da.luu.cuoc.thi'));
        $this->redirect('/admin/jams/' . $id);
    }

    public function addMember(array $params = []) {
        $this->requireRole('admin');
        $jam = $this->jam($params);
        $back = '/admin/jams/' . (int)$jam['ma'] . '#members';
        $login = trim((string)($_POST['nguoi_dung'] ?? ''));
        $role = (string)($_POST['vai_tro_thanh_vien'] ?? '');
        if (!array_key_exists($role, labels('jam_role'))) {
            $this->fail(t('jam.admin.vai.tro.khong.hop.le'), $back);
        }
        $member = Model::one("SELECT ma, ten_hien_thi FROM nguoi_dung WHERE (ten_dang_nhap = :a OR email = :b) AND trang_thai = 'active'", [':a' => $login, ':b' => $login]);
        if (!$member) {
            $this->fail(t('jam.admin.khong.tim.thay.nguoi.dung.dang.hoat.dong.voi.ten.dang.nhap.hoac.email.nay'), $back);
        }
        Model::run('INSERT INTO thanh_vien_cuoc_thi (cuoc_thi_id, nguoi_dung_id, vai_tro_thanh_vien) VALUES (:j, :u, :r)
            ON DUPLICATE KEY UPDATE vai_tro_thanh_vien = VALUES(vai_tro_thanh_vien)', [':j' => (int)$jam['ma'], ':u' => (int)$member['ma'], ':r' => $role]);
        audit('jam_member_set', 'cuoc_thi_lam_game', (int)$jam['ma'], ['user' => (int)$member['ma'], 'role' => $role]);
        NotificationModel::notifyUsers([(int)$member['ma']], current_user_id(), 'jam_member',t('jam.admin.ban.duoc.them.vao.ban.to.chuc', [$jam['tieu_de']]),t('jam.admin.vai.tro', [label('jam_role', $role)]), '/jams/' . rawurlencode($jam['duong_dan_rut_gon']));
        flash('success',t('jam.admin.da.them.voi.vai.tro', [$member['ten_hien_thi'], label('jam_role', $role)]));
        $this->redirect($back);
    }

    public function removeMember(array $params = []) {
        $this->requireRole('admin');
        $jam = $this->jam($params);
        $back = '/admin/jams/' . (int)$jam['ma'] . '#members';
        $userId = (int)($params['user'] ?? 0);
        $role = JamModel::memberRole((int)$jam['ma'], $userId);
        if (!$role) {
            $this->notFound(t('jam.admin.nguoi.nay.khong.thuoc.ban.to.chuc'));
        }
        if ($role === 'host' && (int)Model::scalar("SELECT COUNT(*) FROM thanh_vien_cuoc_thi WHERE cuoc_thi_id = :j AND vai_tro_thanh_vien = 'host'", [':j' => (int)$jam['ma']]) <= 1) {
            $this->fail(t('jam.admin.cuoc.thi.can.it.nhat.mot.nguoi.chu.tri'), $back);
        }
        Model::run('DELETE FROM thanh_vien_cuoc_thi WHERE cuoc_thi_id = :j AND nguoi_dung_id = :u', [':j' => (int)$jam['ma'], ':u' => $userId]);
        audit('jam_member_remove', 'cuoc_thi_lam_game', (int)$jam['ma'], ['user' => $userId]);
        flash('success',t('jam.admin.da.go.khoi.ban.to.chuc'));
        $this->redirect($back);
    }

    /** Disqualify an entry or reinstate it. Withdrawing is the entrant's own action. */
    public function entryStatus(array $params = []) {
        $this->requireRole('admin');
        $jam = $this->jam($params);
        $back = '/admin/jams/' . (int)$jam['ma'] . '#entries';
        $entry = JamModel::entry((int)$jam['ma'], (int)($params['entry'] ?? 0));
        if (!$entry) {
            $this->notFound(t('jam.admin.khong.tim.thay.bai.du.thi'));
        }
        $status = (string)($_POST['trang_thai'] ?? '');
        if (!in_array($status, ['submitted', 'disqualified'], true) || $status === $entry['trang_thai']) {
            $this->fail(t('admin.trang.thai.khong.hop.le'), $back);
        }
        $reason = post_str('ly_do', 500);
        if ($status === 'disqualified' && ($reason === null || $reason === '')) {
            $this->fail(t('jam.admin.hay.ghi.ly.do.loai.bai.toi.da.500.ky.tu.ly.do.duoc.gui.cho.nguoi.nop'), $back);
        }
        Model::run('UPDATE bai_du_thi SET trang_thai = :s WHERE ma = :e', [':s' => $status, ':e' => (int)$entry['ma']]);
        audit('jam_entry_' . $status, 'bai_du_thi', (int)$entry['ma'], ['reason' => $reason]);
        NotificationModel::notifyUsers([(int)$entry['nguoi_nop_id']], current_user_id(), 'jam_entry_status',($status === 'disqualified' ? t('jam.admin.bai.du.thi.bi.loai', [$entry['game_title']]) : t('jam.admin.bai.du.thi.duoc.khoi.phuc', [$entry['game_title']])),($status === 'disqualified' ? t('download.ly.do', [$reason]) : t('jam.admin.bai.cua.ban.da.tro.lai.cuoc.thi', [$jam['tieu_de']])),
            '/jams/' . rawurlencode($jam['duong_dan_rut_gon']));
        flash('success',($status === 'disqualified' ? t('jam.admin.da.loai.bai.du.thi') : t('jam.admin.da.khoi.phuc.bai.du.thi')));
        $this->redirect($back);
    }

    // ------------------------------------------------------------------ helpers

    private function jam(array $params): array {
        $jam = Model::one('SELECT * FROM cuoc_thi_lam_game WHERE ma = :id', [':id' => (int)($params['id'] ?? 0)]);
        if (!$jam) {
            $this->notFound(t('jam.admin.khong.tim.thay.cuoc.thi'));
        }
        return $jam;
    }

    private function validated(?array $jam, string $back): array {
        $errors = [];
        $title = post_str('tieu_de', 200);
        if ($title === null || mb_strlen($title) < 3) {
            $errors[] = t('jam.admin.ten.cuoc.thi.can.3.200.ky.tu');
        }
        $slug = trim((string)($_POST['duong_dan_rut_gon'] ?? ''));
        $slug = $slug === '' ? slugify((string)$title) : $slug;
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) || strlen($slug) > 220) {
            $errors[] = t('jam.admin.duong.dan.chi.gom.chu.thuong.khong.dau.so.va.dau.gach.ngang');
        } elseif (Model::scalar('SELECT 1 FROM cuoc_thi_lam_game WHERE duong_dan_rut_gon = :s AND ma <> :id', [':s' => $slug, ':id' => (int)($jam['ma'] ?? 0)])) {
            $errors[] = t('jam.admin.duong.dan.da.duoc.cuoc.thi.khac.dung', [$slug]);
        }
        $description = post_str('mo_ta', 20000);
        $rules = post_str('the_le', 20000);
        if ($description === null || $rules === null) {
            $errors[] = t('jam.admin.gioi.thieu.va.the.le.toi.da.20.000.ky.tu.moi.phan');
        }
        $status = (string)($_POST['trang_thai'] ?? 'draft');
        if (!in_array($status, JamModel::STATUSES, true)) {
            $errors[] = t('admin.trang.thai.khong.hop.le');
        }
        $start = parse_datetime($_POST['ngay_bat_dau'] ?? '');
        $deadline = parse_datetime($_POST['han_nop_bai'] ?? '');
        $voteStart = parse_datetime($_POST['ngay_bat_dau_binh_chon'] ?? '');
        $voteEnd = parse_datetime($_POST['ngay_ket_thuc_binh_chon'] ?? '');
        $voting = post_bool('bat_binh_chon');
        if (!$start || !$deadline) {
            $errors[] = t('jam.admin.can.ngay.bat.dau.va.han.nop.bai');
        } elseif ($deadline <= $start) {
            $errors[] = t('jam.admin.han.nop.bai.phai.sau.ngay.bat.dau');
        }
        if ($voteStart && $deadline && $voteStart < $deadline) {
            $errors[] = t('jam.admin.binh.chon.chi.mo.sau.han.nop.bai');
        }
        if ($voteEnd && ($voteEnd <= ($voteStart ?: $deadline))) {
            $errors[] = t('jam.admin.ngay.dong.binh.chon.phai.sau.ngay.mo.binh.chon');
        }
        if ($errors) {
            $this->fail(implode(' ', $errors), $back);
        }
        return [
            'tieu_de' => $title,
            'duong_dan_rut_gon' => $slug,
            'mo_ta' => $description !== '' ? $description : null,
            'the_le' => $rules !== '' ? $rules : null,
            'ngay_bat_dau' => $start,
            'han_nop_bai' => $deadline,
            'ngay_bat_dau_binh_chon' => $voting ? $voteStart : null,
            'ngay_ket_thuc_binh_chon' => $voting ? $voteEnd : null,
            'bat_binh_chon' => $voting,
            'trang_thai' => $status,
        ];
    }
}
