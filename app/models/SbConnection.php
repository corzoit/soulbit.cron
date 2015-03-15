<?php

namespace Models;

class SbConnection extends \Phalcon\Mvc\Model
{        
    public $sb_connection_id;
    public $connection_dt;
    public $requester_id;
    public $receiver_id;
    public $requester_relationship_id;
    public $receiver_relationship_id;


    public function initialize()
    {
        $this->belongsTo('requester_id', 'SbMember', 'sb_member_id');
        $this->belongsTo('receiver_id', 'SbMember', 'sb_member_id');
        $this->belongsTo('requester_relationship_id', 'SbRelationship', 'sb_relationship_id');
        $this->belongsTo('receiver_relationship_id', 'SbRelationship', 'sb_relationship_id');
    }
}