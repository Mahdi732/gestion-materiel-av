<?php

namespace Interfaces;

interface ICommande
{
    public function getDetails(): array;
    public function getClientId(): int;
    public function getPaiement(): string;
    public function setDetails(array $details): void;
    public function setClientId(int $clientId): void;
    public function setPaiement(string $paiement): void;
}
