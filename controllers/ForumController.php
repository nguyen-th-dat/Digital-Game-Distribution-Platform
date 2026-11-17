<?php
// =============================================================================
// FORUM CONTROLLER - Tables: dien_dan, chu_de_thao_luan, bai_viet_thao_luan,
// tuong_tac_bai_viet, hanh_dong_kiem_duyet (moderation log)
//
// Boards: one platform board per topic area (staff), at most one board per
// game (its developers) and per jam (staff). Topics and posts are soft-deleted
// via trang_thai; staff can restore them from /admin/comments.
// =============================================================================

class ForumController extends Controller {
    private const TOPICS_PER_PAGE = 25;
    private const POSTS_PER_PAGE = 30;
    private const TOPICS_PER_10_MIN = 3;
    private const POSTS_PER_MINUTE = 6;

    public function index(array $params = []) {
        $uid = current_user_id();
        $boards = ForumModel::boards(is_staff());
        $groups = ['platform' => [], 'game' => [], 'jam' => []];
        foreach ($boards as $b) {
            $groups[$b['loai_dien_dan']][] = $b;
        }
        $myGames = [];
        if ($uid && (has_role('developer') || is_staff())) {
            $myGames = Model::query("SELECT g.ma, g.tieu_de FROM tro_choi g
                WHERE (g.nguoi_tao_id = :u1 OR EXISTS (SELECT 1 FROM tac_gia_tro_choi a WHERE a.tro_choi_id = g.ma AND a.nguoi_dung_id = :u2))
                  AND NOT EXISTS (SELECT 1 FROM dien_dan d WHERE d.tro_choi_id = g.ma) ORDER BY g.tieu_de", [':u1' => $uid, ':u2' => $uid]);
        }
        $this->render('forum/index', [
            'pageTitle' => t('nav.forums'),
            'groups' => $groups,
            'myGames' => $myGames,
            'jamsWithoutBoard' => is_staff() ? Model::query("SELECT j.ma, j.tieu_de FROM cuoc_thi_lam_game j WHERE NOT EXISTS (SELECT 1 FROM dien_dan d WHERE d.cuoc_thi_id = j.ma) ORDER BY j.ngay_bat_dau DESC") : [],
        ]);
    }

    /** Create a board. Platform and jam boards: staff. Game boards: the game's developers. */
    public function create(array $params = []) {
        $user = $this->requireAuth();
        $kind = (string)($_POST['loai_dien_dan'] ?? '');
        $title = post_str('tieu_de', 180);
        $description = post_str('mo_ta', 1000);
        $public = post_bool('cong_khai');
        if ($title === null || $description === null) {
            $this->fail(t('forum.ten.dien.dan.toi.da.180.ky.tu.mo.ta.toi.da.1.000.ky.tu'), '/forums#new');
        }
        $row = ['loai_dien_dan' => $kind, 'tro_choi_id' => null, 'cuoc_thi_id' => null, 'mo_ta' => $description !== '' ? $description : null, 'cong_khai' => $public];
        if ($kind === 'platform') {
            if (!is_staff()) {
                $this->forbidden(t('forum.chi.dieu.hanh.vien.moi.tao.duoc.dien.dan.chung'));
            }
            if ($title === '') {
                $this->fail(t('forum.hay.dat.ten.cho.dien.dan'), '/forums#new');
            }
        } elseif ($kind === 'game') {
            $game = Model::one('SELECT ma, tieu_de FROM tro_choi WHERE ma = :g', [':g' => (int)($_POST['tro_choi_id'] ?? 0)]);
            if (!$game) {
                $this->fail(t('forum.hay.chon.tro.choi'), '/forums#new');
            }
            if (!is_staff() && !GameModel::isAuthor((int)$game['ma'], (int)$user['ma'])) {
                $this->forbidden(t('forum.chi.nha.phat.trien.cua.tro.choi.moi.mo.duoc.dien.dan.cho.no'));
            }
            if (Model::scalar('SELECT 1 FROM dien_dan WHERE tro_choi_id = :g', [':g' => (int)$game['ma']])) {
                $this->fail(t('forum.tro.choi.nay.da.co.dien.dan'), '/forums#new');
            }
            $row['tro_choi_id'] = (int)$game['ma'];
            $title = $title !== '' ? $title : t('forum.thao.luan.ve', [mb_substr($game['tieu_de'], 0, 160)]);
        } elseif ($kind === 'jam') {
            if (!is_staff()) {
                $this->forbidden(t('forum.chi.dieu.hanh.vien.moi.mo.duoc.dien.dan.cho.cuoc.thi'));
            }
            $jam = Model::one('SELECT ma, tieu_de FROM cuoc_thi_lam_game WHERE ma = :j', [':j' => (int)($_POST['cuoc_thi_id'] ?? 0)]);
            if (!$jam) {
                $this->fail(t('forum.hay.chon.cuoc.thi'), '/forums#new');
            }
            if (Model::scalar('SELECT 1 FROM dien_dan WHERE cuoc_thi_id = :j', [':j' => (int)$jam['ma']])) {
                $this->fail(t('forum.cuoc.thi.nay.da.co.dien.dan'), '/forums#new');
            }
            $row['cuoc_thi_id'] = (int)$jam['ma'];
            $title = $title !== '' ? $title : t('forum.thao.luan', [mb_substr($jam['tieu_de'], 0, 166)]);
        } else {
            $this->fail(t('forum.loai.dien.dan.khong.hop.le'), '/forums#new');
        }
        $id = ForumModel::insert($row + ['tieu_de' => $title]);
        audit('forum_create', 'dien_dan', $id, ['loai' => $kind]);
        flash('success',t('forum.da.mo.dien.dan', [$title]));
        $this->redirect('/forums/' . $id);
    }

    public function show(array $params = []) {
        $board = $this->visibleBoard($params);
        $staff = is_staff();
        $q = mb_substr(trim((string)($_GET['q'] ?? '')), 0, 100);
        $page = $this->page();
        $total = ForumModel::countTopics((int)$board['ma'], $staff, $q);
        $this->render('forum/show', [
            'pageTitle' => $board['tieu_de'],
            'board' => $board,
            'topics' => ForumModel::topics((int)$board['ma'], $staff, $q, self::TOPICS_PER_PAGE, ($page - 1) * self::TOPICS_PER_PAGE),
            'q' => $q,
            'page' => $page,
            'pages' => max(1, (int)ceil($total / self::TOPICS_PER_PAGE)),
            'total' => $total,
        ]);
    }

    public function newTopic(array $params = []) {
        $board = $this->visibleBoard($params);
        $this->requireAuth();
        $this->render('forum/new-topic', ['pageTitle' => t('forum.new_topic'), 'board' => $board]);
    }

    public function createTopic(array $params = []) {
        $board = $this->visibleBoard($params);
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $back = '/forums/' . (int)$board['ma'] . '/topics/new';
        $title = post_str('tieu_de', 220);
        $body = post_str('noi_dung', 20000);
        if ($title === null || mb_strlen($title) < 3 || $body === null || $body === '') {
            $this->fail(t('forum.tieu.de.can.3.220.ky.tu.noi.dung.can.1.20.000.ky.tu'), $back);
        }
        $recent = (int)Model::scalar('SELECT COUNT(*) FROM chu_de_thao_luan WHERE tac_gia_nguoi_dung_id = :u AND ngay_tao > NOW() - INTERVAL 10 MINUTE', [':u' => $uid]);
        if ($recent >= self::TOPICS_PER_10_MIN) {
            $this->fail(t('forum.ban.vua.mo.nhieu.chu.de.hay.cho.vai.phut.roi.thu.lai'), $back);
        }
        Model::run('INSERT INTO chu_de_thao_luan (dien_dan_id, tac_gia_nguoi_dung_id, tieu_de, noi_dung_mo_dau) VALUES (:b, :u, :t, :c)', [
            ':b' => (int)$board['ma'], ':u' => $uid, ':t' => $title, ':c' => $body,
        ]);
        $topicId = Model::lastId();
        audit('topic_create', 'chu_de_thao_luan', $topicId);
        if ($board['loai_dien_dan'] === 'game') {
            $authors = array_diff(GameModel::authorIds((int)$board['tro_choi_id']), [$uid]);
            NotificationModel::notifyUsers($authors, $uid, 'forum_topic',t('forum.chu.de.moi.ve', [$board['game_title']]), $title, '/forums/' . (int)$board['ma'] . '/topics/' . $topicId);
        }
        flash('success',t('forum.created'));
        $this->redirect('/forums/' . (int)$board['ma'] . '/topics/' . $topicId);
    }

    public function topic(array $params = []) {
        $board = $this->visibleBoard($params);
        $topic = $this->visibleTopic($board, $params);
        $staff = is_staff();
        $uid = current_user_id();
        $total = ForumModel::countPosts((int)$topic['ma'], $staff);
        $pages = max(1, (int)ceil($total / self::POSTS_PER_PAGE));
        $page = min($this->page(), $pages);
        $posts = ForumModel::posts((int)$topic['ma'], $staff, self::POSTS_PER_PAGE, ($page - 1) * self::POSTS_PER_PAGE);
        [$counts, $mine] = ForumModel::reactions(array_column($posts, 'ma'), $uid);
        $this->render('forum/topic', [
            'pageTitle' => $topic['tieu_de'],
            'board' => $board,
            'topic' => $topic,
            'posts' => $posts,
            'reactionCounts' => $counts,
            'myReactions' => $mine,
            'canManage' => $this->canManageBoard($board),
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
        ]);
    }

    public function reply(array $params = []) {
        $board = $this->visibleBoard($params);
        $topic = $this->visibleTopic($board, $params);
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $self = '/forums/' . (int)$board['ma'] . '/topics/' . (int)$topic['ma'];
        if ((int)$topic['da_khoa'] && !$this->canManageBoard($board)) {
            $this->fail(t('forum.chu.de.nay.da.khoa.khong.nhan.them.tra.loi'), $self);
        }
        if ($topic['trang_thai'] !== 'published') {
            $this->fail(t('forum.chu.de.nay.dang.bi.an'), $self);
        }
        $body = post_str('noi_dung', 20000);
        if ($body === null || $body === '') {
            $this->fail(t('forum.noi.dung.tra.loi.can.1.20.000.ky.tu'), $self . '#reply');
        }
        $parentId = (int)($_POST['parent_id'] ?? 0);
        $parent = null;
        if ($parentId) {
            $parent = Model::one("SELECT ma, tac_gia_nguoi_dung_id FROM bai_viet_thao_luan WHERE ma = :p AND chu_de_id = :t AND trang_thai = 'published'", [':p' => $parentId, ':t' => (int)$topic['ma']]);
            if (!$parent) {
                $this->fail(t('forum.bai.viet.ban.tra.loi.khong.con.hien.thi'), $self);
            }
        }
        $recent = (int)Model::scalar('SELECT COUNT(*) FROM bai_viet_thao_luan WHERE tac_gia_nguoi_dung_id = :u AND ngay_tao > NOW() - INTERVAL 1 MINUTE', [':u' => $uid]);
        if ($recent >= self::POSTS_PER_MINUTE) {
            $this->fail(t('forum.ban.dang.gui.qua.nhanh.hay.cho.mot.phut'), $self . '#reply');
        }
        Model::run('INSERT INTO bai_viet_thao_luan (chu_de_id, tac_gia_nguoi_dung_id, bai_viet_cha_id, noi_dung) VALUES (:t, :u, :p, :c)', [
            ':t' => (int)$topic['ma'], ':u' => $uid, ':p' => $parent ? (int)$parent['ma'] : null, ':c' => $body,
        ]);
        $postId = Model::lastId();
        Model::run('UPDATE chu_de_thao_luan SET ngay_cap_nhat = NOW() WHERE ma = :t', [':t' => (int)$topic['ma']]);
        $count = ForumModel::countPosts((int)$topic['ma'], false);
        $last = max(1, (int)ceil($count / self::POSTS_PER_PAGE));
        $url = $self . ($last > 1 ? '?page=' . $last : '') . '#post-' . $postId;
        $notify = [(int)$topic['tac_gia_nguoi_dung_id']];
        if ($parent) {
            $notify[] = (int)$parent['tac_gia_nguoi_dung_id'];
        }
        NotificationModel::notifyUsers(array_unique($notify), $uid, 'forum_reply',t('forum.da.tra.loi.trong', [$user['ten_hien_thi'], mb_substr($topic['tieu_de'], 0, 120)]), excerpt($body, 160), $url);
        flash('success',t('comment.da.gui.tra.loi'));
        $this->redirect($url);
    }

    /** Topic actions: pin, unpin, lock, unlock (board managers); hide, restore (staff); remove (managers or author); edit (author). */
    public function moderateTopic(array $params = []) {
        $board = $this->visibleBoard($params);
        $topic = $this->visibleTopic($board, $params);
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $self = '/forums/' . (int)$board['ma'] . '/topics/' . (int)$topic['ma'];
        $action = (string)($_POST['action'] ?? '');
        $isAuthor = (int)$topic['tac_gia_nguoi_dung_id'] === $uid;
        $manager = $this->canManageBoard($board);
        $reason = post_str('ly_do', 1000) ?? '';
        $flags = [
            'pin' => ['da_ghim', 1, t('forum.da.ghim.chu.de')],
            'unpin' => ['da_ghim', 0, t('forum.da.bo.ghim')],
            'lock' => ['da_khoa', 1, t('forum.da.khoa.chu.de')],
            'unlock' => ['da_khoa', 0, t('forum.da.mo.khoa')],
        ];
        if (isset($flags[$action])) {
            if (!$manager) {
                $this->forbidden();
            }
            [$col, $value, $message] = $flags[$action];
            Model::run("UPDATE chu_de_thao_luan SET $col = :v WHERE ma = :t", [':v' => $value, ':t' => (int)$topic['ma']]);
            audit('topic_' . $action, 'chu_de_thao_luan', (int)$topic['ma']);
            flash('success', $message);
            $this->redirect($self);
        }
        if ($action === 'edit') {
            if (!$isAuthor) {
                $this->forbidden(t('forum.chi.nguoi.mo.chu.de.moi.sua.duoc.noi.dung'));
            }
            if ((int)$topic['da_khoa'] && !$manager) {
                $this->fail(t('forum.chu.de.da.khoa.nen.khong.sua.duoc'), $self);
            }
            $title = post_str('tieu_de', 220);
            $body = post_str('noi_dung', 20000);
            if ($title === null || mb_strlen($title) < 3 || $body === null || $body === '') {
                $this->fail(t('forum.tieu.de.can.3.220.ky.tu.noi.dung.can.1.20.000.ky.tu'), $self . '#edit-topic');
            }
            Model::run('UPDATE chu_de_thao_luan SET tieu_de = :t, noi_dung_mo_dau = :c WHERE ma = :id', [':t' => $title, ':c' => $body, ':id' => (int)$topic['ma']]);
            audit('topic_edit', 'chu_de_thao_luan', (int)$topic['ma']);
            flash('success',t('forum.da.luu.chu.de'));
            $this->redirect($self);
        }
        $status = ['hide' => 'hidden', 'restore' => 'published', 'remove' => 'removed'][$action] ?? null;
        if (!$status) {
            $this->fail(t('forum.thao.tac.khong.hop.le'), $self);
        }
        $allowed = $action === 'remove' ? ($manager || $isAuthor) : is_staff();
        if (!$allowed) {
            $this->forbidden();
        }
        Model::run('UPDATE chu_de_thao_luan SET trang_thai = :s WHERE ma = :t', [':s' => $status, ':t' => (int)$topic['ma']]);
        if (!$isAuthor || is_staff()) {
            ModerationActionModel::log($uid, 'community_topic', (int)$topic['ma'], $action, $reason);
        }
        audit('topic_' . $action, 'chu_de_thao_luan', (int)$topic['ma']);
        if ($action === 'remove') {
            flash('success',t('forum.da.go.chu.de'));
            $this->redirect('/forums/' . (int)$board['ma']);
        }
        flash('success',($action === 'hide' ? t('forum.da.an.chu.de.khoi.nguoi.dung.thuong') : t('forum.da.khoi.phuc.chu.de')));
        $this->redirect($self);
    }

    public function react(array $params = []) {
        $user = $this->requireAuth();
        $post = $this->postInVisibleTopic($params);
        $url = '/forums/' . (int)$post['dien_dan_id'] . '/topics/' . (int)$post['chu_de_id'];
        $reaction = (string)($_POST['reaction'] ?? '');
        if (!in_array($reaction, ForumModel::REACTIONS, true)) {
            $this->fail(t('forum.kieu.tuong.tac.khong.hop.le'), $url);
        }
        if ((int)$post['tac_gia_nguoi_dung_id'] === (int)$user['ma']) {
            $this->fail(t('forum.ban.khong.the.tuong.tac.voi.bai.cua.chinh.minh'), $url . '#post-' . (int)$post['ma']);
        }
        ForumModel::toggleReaction((int)$post['ma'], (int)$user['ma'], $reaction);
        $this->back($url . '#post-' . (int)$post['ma']);
    }

    public function editPost(array $params = []) {
        $user = $this->requireAuth();
        $post = $this->postInVisibleTopic($params);
        $url = safe_return((string)($_POST['return_to'] ?? ''), '/forums/' . (int)$post['dien_dan_id'] . '/topics/' . (int)$post['chu_de_id']);
        if ((int)$post['tac_gia_nguoi_dung_id'] !== (int)$user['ma']) {
            $this->forbidden(t('forum.ban.chi.sua.duoc.bai.viet.cua.minh'));
        }
        if ((int)$post['da_khoa'] && !is_staff()) {
            $this->fail(t('forum.chu.de.da.khoa.nen.khong.sua.duoc'), $url);
        }
        $body = post_str('noi_dung', 20000);
        if ($body === null || $body === '') {
            $this->fail(t('forum.noi.dung.can.1.20.000.ky.tu'), $url);
        }
        Model::run('UPDATE bai_viet_thao_luan SET noi_dung = :c, ngay_chinh_sua = NOW() WHERE ma = :p', [':c' => $body, ':p' => (int)$post['ma']]);
        audit('post_edit', 'bai_viet_thao_luan', (int)$post['ma']);
        flash('success',t('dev.game.da.luu.bai.viet'));
        $this->redirect($url);
    }

    public function deletePost(array $params = []) {
        $user = $this->requireAuth();
        $post = $this->postInVisibleTopic($params);
        $board = ForumModel::board((int)$post['dien_dan_id']);
        $isAuthor = (int)$post['tac_gia_nguoi_dung_id'] === (int)$user['ma'];
        if (!$isAuthor && !$this->canManageBoard($board)) {
            $this->forbidden();
        }
        Model::run("UPDATE bai_viet_thao_luan SET trang_thai = 'removed' WHERE ma = :p", [':p' => (int)$post['ma']]);
        if (!$isAuthor) {
            ModerationActionModel::log((int)$user['ma'], 'community_post', (int)$post['ma'], 'remove', post_str('ly_do', 1000) ?? '');
        }
        audit($isAuthor ? 'post_delete' : 'post_remove', 'bai_viet_thao_luan', (int)$post['ma']);
        flash('success',t('forum.deleted'));
        $this->back('/forums/' . (int)$post['dien_dan_id'] . '/topics/' . (int)$post['chu_de_id']);
    }

    // ------------------------------------------------------------------ helpers

    private function canSeeBoard(array $board): bool {
        if (is_staff()) {
            return true;
        }
        if (!(int)$board['cong_khai']) {
            return false;
        }
        return match ($board['loai_dien_dan']) {
            'game' => in_array($board['game_status'], ['published', 'unlisted'], true) || GameModel::isAuthor((int)$board['tro_choi_id'], current_user_id()),
            'jam' => !in_array($board['jam_status'], ['draft', 'cancelled'], true),
            default => true,
        };
    }

    /** Staff, the game's developers for a game board, and jam hosts/admins/moderators for a jam board. */
    private function canManageBoard(?array $board): bool {
        $uid = current_user_id();
        if (!$board || !$uid) {
            return false;
        }
        if (is_staff()) {
            return true;
        }
        if ($board['loai_dien_dan'] === 'game') {
            return GameModel::isAuthor((int)$board['tro_choi_id'], $uid);
        }
        if ($board['loai_dien_dan'] === 'jam') {
            return (bool)Model::scalar("SELECT 1 FROM thanh_vien_cuoc_thi WHERE cuoc_thi_id = :j AND nguoi_dung_id = :u AND vai_tro_thanh_vien IN ('host','admin','moderator')", [':j' => (int)$board['cuoc_thi_id'], ':u' => $uid]);
        }
        return false;
    }

    private function visibleBoard(array $params): array {
        $board = ForumModel::board((int)($params['id'] ?? 0));
        if (!$board || !$this->canSeeBoard($board)) {
            $this->notFound(t('forum.khong.tim.thay.dien.dan.nay'));
        }
        return $board;
    }

    private function visibleTopic(array $board, array $params): array {
        $topic = ForumModel::topic((int)$board['ma'], (int)($params['topic'] ?? 0));
        if (!$topic || $topic['trang_thai'] === 'removed' || ($topic['trang_thai'] === 'hidden' && !is_staff())) {
            $this->notFound(t('forum.khong.tim.thay.chu.de.nay'));
        }
        return $topic;
    }

    private function postInVisibleTopic(array $params): array {
        $post = ForumModel::postWithTopic((int)($params['id'] ?? 0));
        $board = $post ? ForumModel::board((int)$post['dien_dan_id']) : null;
        if (!$post || $post['trang_thai'] === 'removed' || ($post['trang_thai'] === 'hidden' && !is_staff())
            || $post['topic_status'] === 'removed' || ($post['topic_status'] === 'hidden' && !is_staff())
            || !$board || !$this->canSeeBoard($board)) {
            $this->notFound(t('forum.khong.tim.thay.bai.viet.nay'));
        }
        return $post;
    }
}
