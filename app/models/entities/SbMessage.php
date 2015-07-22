<?php

namespace SoulbitCron\Models\Entities;

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
        $this->hasMany('sb_message_id', '\SoulbitCron\Models\Entities\SbAttachment', 'sb_message_id', array('alias' => 'SbAttachment'));
        $this->hasMany('sb_message_id', '\SoulbitCron\Models\Entities\SbMessageTag', 'sb_message_id', array('alias' => 'SbMessageTag'));
        $this->hasMany('sb_message_id', '\SoulbitCron\Models\Entities\SbReceiver', 'sb_member_id', array('alias' => 'SbReceiver'));
        $this->belongsTo('sb_sender_member_id', '\SoulbitCron\Models\Entities\SbMember', 'sb_member_id', array('alias' => 'SbMember'));
    }

    public function loadFromArray($data_arr)
    {
        foreach($data_arr as $key => $item)
        {
            if(property_exists($this, $key))
            {
                eval("\$this->".$key." = \$item;");
            }
        }
    }
}