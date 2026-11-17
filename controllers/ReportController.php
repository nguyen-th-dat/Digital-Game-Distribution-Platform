<?php
// =============================================================================
// REPORT CONTROLLER - Table: bao_cao_noi_dung
// Any signed-in user can report content; staff triage it in /admin/reports.
// =============================================================================

class ReportController extends Controller {
    public const TARGETS = [
        'user' => 'nguoi_dung', 'game' => 'tro_choi', 'review' => 'danh_gia', 'game_comment' => 'binh_luan_tro_choi',
        'devlog' => 'nhat_ky_phat_trien', 'community_topic' => 'chu_de_thao_luan', 'community_post' => 'bai_viet_thao_luan',
    ];
    public const REASONS = ['spam', 'abuse', 'illegal', 'copyright', 'malware', 'other'];

    public function create(array $params = []) {
        $user = $this->requireAuth();
        $type = (string)($_POST['type'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        $reason = (string)($_POST['reason'] ?? '');
        $detail = trim((string)($_POST['detail'] ?? ''));
        if (!isset(self::TARGETS[$type]) || $id <= 0) {
            flash('error',t('report.noi.dung.can.bao.cao.khong.hop.le'));
            $this->back('/');
        }
        if (!in_array($reason, self::REASONS, true)) {
            flash('error',t('report.hay.chon.ly.do.bao.cao'));
            $this->back('/');
        }
        if (mb_strlen($detail) > 2000) {
            flash('error',t('report.chi.tiet.toi.da.2.000.ky.tu'));
            $this->back('/');
        }
        $table = self::TARGETS[$type];
        if (!Model::scalar("SELECT 1 FROM `$table` WHERE ma = :id", [':id' => $id])) {
            flash('error',t('report.noi.dung.nay.khong.con.ton.tai'));
            $this->back('/');
        }
        if ($type === 'user' && $id === (int)$user['ma']) {
            flash('error',t('report.ban.khong.the.bao.cao.chinh.minh'));
            $this->back('/');
        }
        $open = Model::scalar("SELECT 1 FROM bao_cao_noi_dung WHERE nguoi_bao_cao_id = :u AND loai_doi_tuong = :t AND doi_tuong_id = :id AND trang_thai IN ('open','investigating')", [
            ':u' => (int)$user['ma'], ':t' => $type, ':id' => $id,
        ]);
        if ($open) {
            flash('info',t('report.ban.da.bao.cao.noi.dung.nay.dieu.hanh.vien.dang.xem.xet'));
            $this->back('/');
        }
        Model::run('INSERT INTO bao_cao_noi_dung (nguoi_bao_cao_id, loai_doi_tuong, doi_tuong_id, ma_ly_do, chi_tiet) VALUES (:u, :t, :id, :r, :d)', [
            ':u' => (int)$user['ma'], ':t' => $type, ':id' => $id, ':r' => $reason, ':d' => $detail !== '' ? $detail : null,
        ]);
        $reportId = Model::lastId();
        $staff = Model::column("SELECT DISTINCT x.nguoi_dung_id FROM nguoi_dung_vai_tro x JOIN vai_tro r ON r.ma = x.vai_tro_id WHERE r.ma_vai_tro IN ('moderator','admin')");
        NotificationModel::notifyUsers($staff, (int)$user['ma'], 'report',t('report.bao.cao.moi', [label('report_target', $type)]),t('download.ly.do', [label('report_reason', $reason)]), '/admin/reports/' . $reportId);
        flash('success',t('report.cam.on.ban.bao.cao.da.duoc.gui.toi.dieu.hanh.vien'));
        $this->back('/');
    }
}
