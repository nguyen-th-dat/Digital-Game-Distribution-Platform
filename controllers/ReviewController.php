<?php
// =============================================================================
// REVIEW CONTROLLER - Tables: danh_gia, binh_chon_danh_gia
// One review per player per game (uq_reviews_user_game). Authors cannot review
// their own game. Editing never lifts a moderator's hide/remove.
// =============================================================================

class ReviewController extends Controller {

    public function save(array $params = []) {
        $user = $this->requireAuth();
        $game = $this->publishedGame($params);
        $uid = (int)$user['ma'];
        $gid = (int)$game['ma'];
        $to = '/games/' . rawurlencode($game['duong_dan_rut_gon']) . '#reviews';

        if (!(int)$game['cho_phep_danh_gia']) {
            $this->fail(t('game.show.nha_phat_trien_da_tat'), $to);
        }
        if (GameModel::isAuthor($gid, $uid)) {
            $this->fail(t('review.ban.khong.the.danh.gia.tro.choi.do.chinh.minh.phat.trien'), $to);
        }
        $rating = (int)($_POST['diem'] ?? 0);
        $title = trim((string)($_POST['tieu_de'] ?? ''));
        $body = trim((string)($_POST['noi_dung'] ?? ''));
        if ($rating < 1 || $rating > 5) {
            $this->fail(t('review.hay.chon.diem.tu.1.den.5'), $to);
        }
        if (mb_strlen($title) > 200) {
            $this->fail(t('review.tieu.de.toi.da.200.ky.tu'), $to);
        }
        if (mb_strlen($body) > 10000) {
            $this->fail(t('review.nhan.xet.toi.da.10.000.ky.tu'), $to);
        }
        $verified = OwnershipModel::has($uid, $gid) ? 1 : 0;
        $existing = Model::one('SELECT ma FROM danh_gia WHERE tro_choi_id = :g AND nguoi_dung_id = :u', [':g' => $gid, ':u' => $uid]);
        if ($existing) {
            Model::run('UPDATE danh_gia SET diem_danh_gia = :r, tieu_de = :t, noi_dung_danh_gia = :b, da_xac_minh_so_huu = :v WHERE ma = :id', [
                ':r' => $rating, ':t' => $title !== '' ? $title : null, ':b' => $body !== '' ? $body : null, ':v' => $verified, ':id' => (int)$existing['ma'],
            ]);
            flash('success',t('review.da.cap.nhat.danh.gia.cua.ban'));
        } else {
            Model::run('INSERT INTO danh_gia (tro_choi_id, nguoi_dung_id, diem_danh_gia, tieu_de, noi_dung_danh_gia, da_xac_minh_so_huu) VALUES (:g, :u, :r, :t, :b, :v)', [
                ':g' => $gid, ':u' => $uid, ':r' => $rating, ':t' => $title !== '' ? $title : null, ':b' => $body !== '' ? $body : null, ':v' => $verified,
            ]);
            NotificationModel::notifyUsers(GameModel::authorIds($gid), $uid, 'review',t('review.danh.gia.moi.cho', [$game['tieu_de']]),t('review.cham.5', [$user['ten_hien_thi'], $rating]), '/games/' . $game['duong_dan_rut_gon'] . '#reviews');
            flash('success',t('review.cam.on.ban.da.danh.gia'));
        }
        $this->redirect($to);
    }

    public function delete(array $params = []) {
        $user = $this->requireAuth();
        $game = $this->publishedGame($params);
        $deleted = Model::run('DELETE FROM danh_gia WHERE tro_choi_id = :g AND nguoi_dung_id = :u', [':g' => (int)$game['ma'], ':u' => (int)$user['ma']]);
        flash($deleted ? 'success' : 'info',($deleted ? t('review.da.xoa.danh.gia.cua.ban') : t('review.ban.chua.co.danh.gia.nao.de.xoa')));
        $this->redirect('/games/' . rawurlencode($game['duong_dan_rut_gon']) . '#reviews');
    }

    /** Helpful / not helpful. The same choice twice removes the vote. */
    public function vote(array $params = []) {
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $review = Model::one("SELECT r.ma, r.nguoi_dung_id, g.duong_dan_rut_gon FROM danh_gia r JOIN tro_choi g ON g.ma = r.tro_choi_id
            WHERE r.ma = :id AND r.trang_thai = 'published' AND g.trang_thai IN ('published','unlisted')", [':id' => (int)($params['id'] ?? 0)]);
        if (!$review) {
            $this->notFound(t('review.khong.tim.thay.danh.gia.nay'));
        }
        $fallback = '/games/' . rawurlencode($review['duong_dan_rut_gon']) . '#review-' . (int)$review['ma'];
        if ((int)$review['nguoi_dung_id'] === $uid) {
            flash('error',t('review.ban.khong.the.binh.chon.cho.danh.gia.cua.chinh.minh'));
            $this->back($fallback);
        }
        $helpful = ($_POST['co_ich'] ?? '') === '1' ? 1 : 0;
        $current = Model::scalar('SELECT co_ich FROM binh_chon_danh_gia WHERE danh_gia_id = :r AND nguoi_dung_id = :u', [':r' => (int)$review['ma'], ':u' => $uid]);
        if ($current !== null && $current !== false && (int)$current === $helpful) {
            Model::run('DELETE FROM binh_chon_danh_gia WHERE danh_gia_id = :r AND nguoi_dung_id = :u', [':r' => (int)$review['ma'], ':u' => $uid]);
        } else {
            Model::run('INSERT INTO binh_chon_danh_gia (danh_gia_id, nguoi_dung_id, co_ich) VALUES (:r, :u, :h) ON DUPLICATE KEY UPDATE co_ich = VALUES(co_ich)', [
                ':r' => (int)$review['ma'], ':u' => $uid, ':h' => $helpful,
            ]);
        }
        $this->back($fallback);
    }
}
