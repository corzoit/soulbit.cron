<?php

namespace SoulboxCron\Models\Entities;

class SbReminderEmail extends \Phalcon\Mvc\Model
{
    public $sb_reminder_email_id;
    public $sb_reminder_id;
    public $sb_message_id;
    public $pubid;
    public $receiver_email;
    public $subject;
    public $message;
    public $sent_dt;
    public $processed;

    public function initialize()
    {
        $this->belongsTo('sb_reminder_id', '\SoulboxCron\Models\Entities\SbReminder', 'sb_reminder_id', array('alias' => 'SbMember'));
        $this->belongsTo('sb_message_id', '\SoulboxCron\Models\Entities\SbMessage', 'sb_message_id', array('alias' => 'SbMember'));
    }
}