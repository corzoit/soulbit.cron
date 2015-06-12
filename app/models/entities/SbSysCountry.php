<?php

namespace SoulboxCron\Models\Entities;

class SbCountry extends \Phalcon\Mvc\Model
{
    public $sb_sys_country_id;
    public $country;
    public $country_2code;
    public $call_code;
    public $native_name;
    public $native_name_html;
    public $languages;
    public $timezone;
    public $region;
    public $sub_region;
    public $currencies;
    public $status;

    public function initialize()
    {
        $this->hasMany('sb_sys_country_id', '\SoulboxCron\Models\Entities\SbMember', 'sb_sys_country_id', array('alias' => 'SbMember'));
    }
}