<?php

namespace \SoulbitCron\Models\Entities;

class SbAttachment extends \Phalcon\Mvc\Model
{
    public $sb_attachment_id;
    public $sb_message_id;
    public $file;
    public $mime;
    public $size_kb;
    public $story;
    public $position;

    public function initialize()
    {
        $this->belongsTo('sb_message_id', '\SoulbitCron\Models\Entities\SbMessage', 'sb_message_id', array('alias' => 'SbMessage'));
    }
}