<?php
namespace Utilities\Cron;

use \SoulboxCron\Models\Repositories\SbSysQueueMaster as RepoQueueMaster;

/**
 * Queue master based on a db table
 * Woeks under Phalcon framework environment
 * @author alexcorzo@gmail.com
 * @url http://www.corzoit.com/
 * @license http://creativecommons.org/licenses/by-sa/3.0/us/
 * @version 0.1
 */

class QueueMaster {

    private $file;
    private $description;

    private $pid;
    private $db_pid;
    private $sb_sys_queue_master_id;    

    public function __construct($file, $description = 'none')
    {
        $this->description = $description;
        $this->setFile($file);
        $this->pid = posix_getpid();

        $repo = new RepoQueueMaster();
        $queue_master = $repo->getByFileName($this->file);

        //process has never started
        if(!$queue_master)
        {
            $queue_master = $this->createDefaultRecord();            
        }

        $this->db_pid = $queue_master->pid;
        $this->sb_sys_queue_master_id = $queue_master->sb_sys_queue_master_id;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function getDbPid()
    {
        return $this->db_pid;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function isRunning()
    {
        return file_exists( "/proc/".$this->getDbPid() );    
    }

    public function updatePid()
    {
        if($this->getDbPid() != $this->pid)
        {
            $repo = new RepoQueueMaster();
            echo("\n\nID = *".$this->sb_sys_queue_master_id."*\n\n");
            $repo->updatePid($this->sb_sys_queue_master_id, $this->pid);
            return true;
        }

        return false;
    }

    private function createDefaultRecord()
    {
        $repo = new RepoQueueMaster();
        $queue_master = $repo->createProcess($this->file, $this->description, $this->pid);
        if(!$queue_master)
        {
            throw new Exception('A queue master record could not be created');    
        }
        
        return $queue_master;     
    }
}