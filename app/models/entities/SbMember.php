<?php

namespace SoulboxCron\Models\Entities;

class SbMember extends \Phalcon\Mvc\Model
{
    public $sb_member_id;
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
    public $zip_code;
    public $country;
    public $country_code;
    public $tz;
    public $dt_format;
    public $lang;
    public $salt;
    public $summary_word_count;
    public $registration_ip;
    public $status;

    public function initialize()
    {
        $this->hasMany('sb_member_id', 'SbMessage', 'sb_member_id');
        $this->hasMany('sb_member_id', 'SbReceiver', 'sb_member_id');
        $this->hasMany('sb_member_id', 'SbTag', 'sb_member_id');
        $this->hasMany('sb_member_id', 'SbConnection', 'requester_id');
        $this->hasMany('sb_member_id', 'SbConnection', 'receiver_id');
        $this->hasMany('sb_member_id', 'SbActionLog', 'sb_member_id');
    }
}