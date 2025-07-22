<?php

namespace Interfaces;

interface IMateriel
{
    public function type(): ?string;

    public function etat(): ?string;

    public function disponibilite(): bool;
}
