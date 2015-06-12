<?php

namespace SoulboxCron\Models\Entities;

class SbMember extends \Phalcon\Mvc\Model
{
    public $sb_member_id;
    public $sb_sys_country_id;
    public $creation_dt;
    public $avatar;
    public $alias;
    public $first_name;
    public $last_name;
    public $dob;
    public $gender;
    public $email;
    public $password;
    public $email_changed_dt;
    public $mobile;
    public $mobile_os;
    public $address;
    public $city;
    public $zip_code;
    public $tz;
    public $dt_format;
    public $lang;
    public $salt;
    public $summary_word_count;
    public $registration_ip;
    public $status;

    public function initialize()
    {
        $this->hasMany('sb_member_id', '\SoulboxCron\Models\Entities\SbMessage', 'sb_member_id', array('alias' => 'SbMessage'));
        $this->hasMany('sb_member_id', '\SoulboxCron\Models\Entities\SbReceiver', 'sb_member_id', array('alias' => 'SbReceiver'));
        $this->hasMany('sb_member_id', '\SoulboxCron\Models\Entities\SbTag', 'sb_member_id', array('alias' => 'SbTag'));
        $this->hasMany('sb_member_id', '\SoulboxCron\Models\Entities\SbConnection', 'requester_id', array('alias' => 'SbConnectionRequester'));
        $this->hasMany('sb_member_id', '\SoulboxCron\Models\Entities\SbConnection', 'receiver_id', array('alias' => 'SbConnectionReceiver'));
        $this->hasMany('sb_member_id', '\SoulboxCron\Models\Entities\SbActionLog', 'sb_member_id', array('alias' => 'SbActionLog'));
        $this->hasMany('sb_member_id', '\SoulboxCron\Models\Entities\SbReminder', 'sb_member_id', array('alias' => 'SbReminder'));

        $this->belongsTo('sb_sys_country_id', '\SoulboxCron\Models\Entities\SbSysCountry', 'sb_sys_country_id', array('alias' => 'SbSysCountry'));
    }
}