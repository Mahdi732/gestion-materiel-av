<?php
namespace Services;

use Interfaces\IMateriel;
use Models\Camera;
use Models\Microphone;
use Models\Projecteur;
use Exception;

class MaterielTypeResolver {
    private static array $typeMap = [
        1 => Camera::class,
        2 => Projecteur::class,
        3 => Microphone::class,
    ];

    public static function resolve(int $typeId): IMateriel {
        if (!isset(self::$typeMap[$typeId])) {
            throw new Exception("Type matériel inconnu : $typeId");
        }

        $className = self::$typeMap[$typeId];
        return new $className();
    }
}
