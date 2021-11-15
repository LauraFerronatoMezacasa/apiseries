<?php

namespace Source\Models;
use CoffeeCode\DataLayer\DataLayer;

class Episodio extends DataLayer {
    public function __construct() {
        parent::__construct('episodios_info', [], 'id',false);
    }

}