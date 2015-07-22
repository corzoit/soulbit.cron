<?php

namespace SoulbitCron\Models\Entities;

class SbSysQueueMaster extends \Phalcon\Mvc\Model {

    public $sb_sys_queue_master_id;
    public $file;
    public $pid;
    public $description;
    public $last_init_dt;

	public function initialize() {
        $this->setSource("sb_sys_queue_master");

        $this->useDynamicUpdate(true);
        $this->keepSnapshots(true);

		
	}



}
