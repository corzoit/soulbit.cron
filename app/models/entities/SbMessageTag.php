<?php

namespace SoulboxCron\Models\Entities;

class SbMessageTag extends \Phalcon\Mvc\Model
{    
    public $sb_message_id;
    public $sb_tag_id;

    public function initialize()
    {
        $this->belongsTo('sb_message_id', 'SbMessage', 'sb_message_id');
        $this->belongsTo('sb_tag_id', 'SbTag', 'sb_tag_id');
    }
}