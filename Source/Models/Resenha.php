<?php

namespace Source\Models;
use CoffeeCode\DataLayer\DataLayer;

class Resenha extends DataLayer {
    public function __construct() {
        parent::__construct('resenhas', [], 'id', false);
    }
}