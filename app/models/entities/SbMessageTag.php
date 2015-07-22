<?php

namespace SoulbitCron\Models\Entities;

class SbMessageTag extends \Phalcon\Mvc\Model
{    
    public $sb_message_id;
    public $sb_tag_id;

    public function initialize()
    {
        $this->belongsTo('sb_message_id', '\SoulbitCron\Models\Entities\SbMessage', 'sb_message_id', array('alias' => 'SbMessage'));
        $this->belongsTo('sb_tag_id', '\SoulbitCron\Models\Entities\SbTag', 'sb_tag_id', array('alias' => 'SbTag'));
    }
}