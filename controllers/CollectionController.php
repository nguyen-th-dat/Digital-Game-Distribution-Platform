<?php
// =============================================================================
// COLLECTION CONTROLLER - Tables: bo_suu_tap, tro_choi_bo_suu_tap
// Players curate public, unlisted or private lists of games.
// =============================================================================

class CollectionController extends Controller {
    private const MAX_PER_USER = 50;
    private const MAX_GAMES = 200;
    private const PER_PAGE = 24;

    public function index(array $params = []) {
        $uid = current_user_id();
        $page = $this->page();
        $total = CollectionModel::countCommunity();
        $this->render('collection/index', [
            'pageTitle' => t('profile.collections'),
            'mine' => $uid ? CollectionModel::forOwner($uid) : [],
            'community' => CollectionModel::community(self::PER_PAGE, ($page - 1) * self::PER_PAGE),
            'page' => $page,
            'pages' => max(1, (int)ceil($total / self::PER_PAGE)),
            'total' => $total,
        ]);
    }

    public function create(array $params = []) {
        $user = $this->requireAuth();
        $uid = (int)$user['ma'];
        $back = safe_return((string)($_POST['return_to'] ?? ''), '/collections');
        $data = $this->validated($back);
        if ((int)Model::scalar('SELECT COUNT(*) FROM bo_suu_tap WHERE nguoi_dung_so_huu_id = :u', [':u' => $uid]) >= self::MAX_PER_USER) {
            $this->fail(t('collection.moi.tai.khoan.co.toi.da.bo.suu.tap', [self::MAX_PER_USER]), $back);
        }
        $gameId = (int)($_POST['game_id'] ?? 0);
        $game = $gameId ? $this->visibleGame($gameId) : null;
        if ($gameId && !$game) {
            $this->fail(t('collection.tro.choi.nay.khong.con.kha.dung'), $back);
        }
        $id = Model::transaction(function () use ($uid, $data, $game) {
            $id = CollectionModel::insert($data + [
                'nguoi_dung_so_huu_id' => $uid,
                'duong_dan_rut_gon' => CollectionModel::uniqueSlug($uid, $data['tieu_de']),
            ]);
            if ($game) {
                CollectionModel::addGame($id, (int)$game['ma'], $uid, null);
            }
            return $id;
        });
        audit('collection_create', 'bo_suu_tap', $id);
        if ($game) {
            flash('success',t('collection.da.tao.va.them', [$data['tieu_de'], $game['tieu_de']]));
            $this->redirect($back);
        }
        flash('success',t('collection.da.tao.bo.suu.tap.them.tro.choi.tu.trang.cua.tung.tro.choi'));
        $this->redirect('/collections/' . $id);
    }

    public function show(array $params = []) {
        $collection = $this->visibleCollection($params);
        $isOwner = (int)$collection['nguoi_dung_so_huu_id'] === current_user_id();
        $games = CollectionModel::games((int)$collection['ma']);
        $uid = current_user_id();
        $this->render('collection/show', [
            'pageTitle' => $collection['tieu_de'],
            'collection' => $collection,
            'games' => $games,
            'isOwner' => $isOwner,
            'owned' => $uid ? OwnershipModel::ownedIds($uid, array_map('intval', array_column($games, 'ma'))) : [],
        ]);
    }

    public function update(array $params = []) {
        $collection = $this->ownCollection($params);
        $id = (int)$collection['ma'];
        $data = $this->validated('/collections/' . $id . '#edit');
        if ($data['tieu_de'] !== $collection['tieu_de']) {
            $data['duong_dan_rut_gon'] = CollectionModel::uniqueSlug((int)$collection['nguoi_dung_so_huu_id'], $data['tieu_de'], $id);
        }
        CollectionModel::update($id, $data);
        audit('collection_update', 'bo_suu_tap', $id);
        flash('success',t('collection.da.luu.bo.suu.tap'));
        $this->redirect('/collections/' . $id);
    }

    public function delete(array $params = []) {
        $collection = $this->ownCollection($params);
        if (!hash_equals('XOA', strtoupper(trim((string)($_POST['confirm'] ?? ''))))) {
            $this->fail(t('collection.nhap.xoa.de.xac.nhan.xoa.bo.suu.tap'), '/collections/' . (int)$collection['ma'] . '#danger');
        }
        // tro_choi_bo_suu_tap rows go with it (ON DELETE CASCADE); the games themselves are untouched.
        CollectionModel::delete((int)$collection['ma']);
        audit('collection_delete', 'bo_suu_tap', (int)$collection['ma'], ['tieu_de' => $collection['tieu_de']]);
        flash('success',t('collection.da.xoa.bo.suu.tap', [$collection['tieu_de']]));
        $this->redirect('/collections');
    }

    public function addItem(array $params = []) {
        $collection = $this->ownCollection($params);
        $id = (int)$collection['ma'];
        $back = safe_return((string)($_POST['return_to'] ?? ''), '/collections/' . $id);
        $game = $this->visibleGame((int)($_POST['game_id'] ?? 0));
        if (!$game) {
            $this->fail(t('collection.tro.choi.nay.khong.con.kha.dung'), $back);
        }
        $note = post_str('ghi_chu', 500);
        if ($note === null) {
            $this->fail(t('collection.ghi.chu.toi.da.500.ky.tu'), $back);
        }
        $isNew = !Model::scalar('SELECT 1 FROM tro_choi_bo_suu_tap WHERE bo_suu_tap_id = :c AND tro_choi_id = :g', [':c' => $id, ':g' => (int)$game['ma']]);
        if ($isNew && (int)Model::scalar('SELECT COUNT(*) FROM tro_choi_bo_suu_tap WHERE bo_suu_tap_id = :c', [':c' => $id]) >= self::MAX_GAMES) {
            $this->fail(t('collection.mot.bo.suu.tap.co.toi.da.tro.choi', [self::MAX_GAMES]), $back);
        }
        // The note field is only sent from the collection page; the game page sends none.
        CollectionModel::addGame($id, (int)$game['ma'], current_user_id(), array_key_exists('ghi_chu', $_POST) ? $note : null);
        flash('success',($isNew ? t('collection.da.them.vao', [$game['tieu_de'], $collection['tieu_de']]) : t('collection.da.cap.nhat.ghi.chu.cho', [$game['tieu_de']])));
        $this->redirect($back);
    }

    public function removeItem(array $params = []) {
        $collection = $this->ownCollection($params);
        $id = (int)$collection['ma'];
        $removed = CollectionModel::removeGame($id, (int)($params['game'] ?? 0));
        flash($removed ? 'success' : 'info',($removed ? t('collection.da.bo.tro.choi.khoi.bo.suu.tap') : t('collection.tro.choi.nay.khong.co.trong.bo.suu.tap')));
        $this->back('/collections/' . $id);
    }

    // ------------------------------------------------------------------ helpers

    /** Validated title, description, visibility and layout from POST. */
    private function validated(string $back): array {
        $title = post_str('tieu_de', 180);
        $description = post_str('mo_ta', 5000);
        $visibility = (string)($_POST['pham_vi_hien_thi'] ?? 'public');
        $layout = (string)($_POST['bo_cuc'] ?? 'grid');
        $errors = [];
        if ($title === null || $title === '') {
            $errors[] = t('collection.ten.bo.suu.tap.can.1.180.ky.tu');
        }
        if ($description === null) {
            $errors[] = t('collection.mo.ta.toi.da.5.000.ky.tu');
        }
        if (!in_array($visibility, CollectionModel::VISIBILITY, true)) {
            $errors[] = t('collection.pham.vi.hien.thi.khong.hop.le');
        }
        if (!in_array($layout, CollectionModel::LAYOUTS, true)) {
            $errors[] = t('collection.bo.cuc.khong.hop.le');
        }
        if ($errors) {
            remember_input($_POST);
            $this->fail(implode(' ', $errors), $back);
        }
        return ['tieu_de' => $title, 'mo_ta' => $description !== '' ? $description : null, 'pham_vi_hien_thi' => $visibility, 'bo_cuc' => $layout];
    }

    /** Public and unlisted collections are readable by anyone with the link; private ones only by the owner. */
    private function visibleCollection(array $params): array {
        $collection = CollectionModel::withOwner((int)($params['id'] ?? 0));
        $isOwner = $collection && (int)$collection['nguoi_dung_so_huu_id'] === current_user_id();
        if (!$collection || (!$isOwner && ($collection['pham_vi_hien_thi'] === 'private' || $collection['owner_status'] !== 'active'))) {
            $this->notFound(t('collection.khong.tim.thay.bo.suu.tap.nay'));
        }
        return $collection;
    }

    private function ownCollection(array $params): array {
        $this->requireAuth();
        $collection = CollectionModel::withOwner((int)($params['id'] ?? 0));
        if (!$collection) {
            $this->notFound(t('collection.khong.tim.thay.bo.suu.tap.nay'));
        }
        if ((int)$collection['nguoi_dung_so_huu_id'] !== current_user_id()) {
            $this->forbidden(t('collection.chi.chu.bo.suu.tap.moi.chinh.sua.duoc'));
        }
        return $collection;
    }

    private function visibleGame(int $gameId): ?array {
        return $gameId > 0 ? Model::one("SELECT ma, tieu_de FROM tro_choi WHERE ma = :g AND trang_thai IN ('published','unlisted')", [':g' => $gameId]) : null;
    }
}
