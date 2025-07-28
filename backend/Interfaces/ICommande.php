<?php

namespace Interfaces;

interface ICommande
{
    public function getDetails(): array;

    public function getClient(): object;

    public function getPaiement(): object;
}
