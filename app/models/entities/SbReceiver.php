<?php

namespace SoulbitCron\Models\Entities;

class SbReceiver extends \Phalcon\Mvc\Model
{    
    public $sb_message_id;
    public $sb_member_id;
    public $read_dt;

    public function initialize()
    {
        $this->belongsTo('sb_message_id', '\SoulbitCron\Models\Entities\SbMessage', 'sb_message_id', array('alias' => 'SbTag'));
        $this->belongsTo('sb_member_id', '\SoulbitCron\Models\Entities\SbMember', 'sb_member_id', array('alias' => 'SbTag'));
    }
}