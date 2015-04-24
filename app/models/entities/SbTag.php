<?php

namespace SoulboxCron\Models\Entities;

class SbTag extends \Phalcon\Mvc\Model
{        
    public $sb_tag_id;
    public $sb_member_id;
    public $tag;

    public function initialize()
    {
        $this->belongsTo('sb_member_id', 'SbMember', 'sb_member_id', array('alias' => 'SbMember'));
        $this->hasMany('sb_tag_id', 'SbTagMessage', 'sb_tag_id', array('alias' => 'SbTagMessage'));
    }
}