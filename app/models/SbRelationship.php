<?php

namespace Models;

class SbConnection extends \Phalcon\Mvc\Model
{        
    public $sb_relationship_id;
    public $name;
    public $inverse_male;
    public $inverse_female;

    public function initialize()
    {
    }
}