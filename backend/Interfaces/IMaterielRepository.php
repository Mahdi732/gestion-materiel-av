<?php

namespace Interfaces;

use Models\MaterielBase;

interface IMaterielRepository {
    public function getAll () : array;
    public function changerDisponibilite (int $id, bool $disponible) : bool;
    public function findById (int $id) : ?MaterielBase;
    public function save (MaterielBase $material) : bool;
    public function delete (int $id) : bool;
}