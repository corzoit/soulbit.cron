<?php

namespace SoulboxCron\Models\Entities;

class SbMessage extends \Phalcon\Mvc\Model
{
    public $sb_message_id;
    public $sb_sender_member_id;
    public $creation_dt;
    public $mailer;
    public $pubid;
    public $message_type;
    public $subject;
    public $message;
    public $summary;
    public $delivery_type;
    public $delivery_dt;
    public $delivery_age;
    public $delivery_age_day_offset;

    public function initialize()
    {
        $this->hasMany('sb_message_id', '\SoulboxCron\Models\Entities\SbAttachment', 'sb_message_id', array('alias' => 'SbAttachment'));
        $this->hasMany('sb_message_id', '\SoulboxCron\Models\Entities\SbMessageTag', 'sb_message_id', array('alias' => 'SbMessageTag'));
        $this->hasMany('sb_message_id', '\SoulboxCron\Models\Entities\SbReceiver', 'sb_member_id', array('alias' => 'SbReceiver'));
        $this->belongsTo('sb_sender_member_id', '\SoulboxCron\Models\Entities\SbMember', 'sb_member_id', array('alias' => 'SbMember'));
    }
}