<?php

namespace Interfaces;

use Models\MaterielBase;

interface IMaterielRepository {
    public function getAll () : array;
    public function findById (int $id) : ?MaterielBase;
    public function save (MaterielBase $material) : bool;
    public function delete (int $id) : bool;
}