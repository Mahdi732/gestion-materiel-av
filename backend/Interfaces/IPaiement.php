<?php
namespace Interfaces;

interface IPaiement
{
    public function payer(float $montant): bool;
}
