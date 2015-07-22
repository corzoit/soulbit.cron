<?php

namespace SoulbitCron\Models\Entities;

class SbReminder extends \Phalcon\Mvc\Model
{
    public $sb_reminder_id;
    public $sb_member_id;
    public $subject;
    public $message;
    public $frequency_t;
    public $frequency_v;
    public $frequency_time;    
    public $status;

    public function initialize()
    {
        $this->belongsTo('sb_member_id', '\SoulbitCron\Models\Entities\SbMember', 'sb_member_id', array('alias' => 'SbMember'));
    }
}