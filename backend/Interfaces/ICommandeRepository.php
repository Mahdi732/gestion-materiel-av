<?php

namespace Interfaces;

use Models\CommandeVente;

interface ICommandeRepository {
    public function getAll () : array;
    public function findByUser (int $id) : ?array;
    public function save (CommandeVente $commande) : bool;
    public function delete (int $id) : bool;
    public function changeStatus (int $commandeId, string $newStatus) : bool;
}