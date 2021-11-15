<?php

namespace Source\Models;
use CoffeeCode\DataLayer\DataLayer;

class Serie extends DataLayer {

    public function __construct() {
        parent::__construct("series", []);
    }
}