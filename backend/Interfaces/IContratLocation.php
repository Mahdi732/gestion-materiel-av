<?php

namespace Interfaces;

interface IContratLocation
{
    public function getDuree(): int; 
    public function getClientId(): int;
    public function getMaterielId(): int;
}
