<?php
namespace Patterns;

use Interfaces\IPaiement;

class PaiementCB implements IPaiement {
    public function payer(float $montant): bool {
        return true;
    }
}
