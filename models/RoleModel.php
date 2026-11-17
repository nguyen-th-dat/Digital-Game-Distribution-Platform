<?php
// =============================================================================
// ROLE MODEL - Table: vai_tro
// =============================================================================

class RoleModel extends Model {
    protected static $table = 'vai_tro';

    public static function getAll(): array {
        return self::all();
    }

    public static function findByCode(string $code): ?array {
        return self::findBy('ma_vai_tro', $code);
    }

    public static function ensureDefaults(): void {
        $defaults = ['player', 'developer', 'moderator', 'admin'];
        foreach ($defaults as $code) {
            $name = label('role', $code);
            $exists = self::findBy('ma_vai_tro', $code);
            if (!$exists) {
                self::insert(['ma_vai_tro' => $code, 'ten_vai_tro' => $name]);
            }
        }
    }
}