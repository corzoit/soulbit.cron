<?php

namespace SoulbitCron\Models\Repositories;

use \SoulbitCron\Models\Entities\SbSysQueueMaster as EntityQueueMaster;

class SbSysQueueMaster
{
    public function __construct()
    {
        
    }

    public function getByFileName($file)
    {
        return EntityQueueMaster::findFirst(array(                
            'columns'    => '*',
            'conditions' => 'file = ?1',
            'bind'       => array(1 => $file)
         ));
    }

    public function createProcess($file, $description, $pid)
    {       
        $queue_master = new EntityQueueMaster();        
        $queue_master->sb_sys_queue_master_id = null;
        $queue_master->file = $file;
        $queue_master->description = $description;
        $queue_master->pid = $pid;
        $queue_master->last_init_dt = date('Y-m-d H:i:s');

        if($queue_master->save())
        {
            return $queue_master;
        }
        else
        {
            return false;
        }
    }

    public function updatePid($sb_sys_queue_master_id, $pid)
    {       
        $queue_master = EntityQueueMaster::findFirst($sb_sys_queue_master_id);
        $queue_master->pid = $pid;
        $queue_master->last_init_dt = date('Y-m-d H:i:s');

        if($queue_master->update()) 
        {
            return $queue_master;
        }
        else
        {
            return false;
        }
    }      
}