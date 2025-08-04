<?php
namespace Patterns;

use Interfaces\IPaiement;

class PaiementVirement implements IPaiement {
    public function payer(float $montant): bool {
        return true;
    }
}
