<?php

namespace SoulbitCron\Models\Entities;

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
        $this->belongsTo('requester_id', '\SoulbitCron\Models\Entities\SbMember', 'sb_member_id', array('alias' => 'SbMemberRequester'));
        $this->belongsTo('receiver_id', '\SoulbitCron\Models\Entities\SbMember', 'sb_member_id', array('alias' => 'SbMemberReceiver'));
        $this->belongsTo('requester_relationship_id', '\SoulbitCron\Models\Entities\SbRelationship', 'sb_relationship_id', array('alias' => 'SbRelationshipRequester'));
        $this->belongsTo('receiver_relationship_id', '\SoulbitCron\Models\Entities\SbRelationship', 'sb_relationship_id', array('alias' => 'SbRelationshipReceiver'));
    }
}