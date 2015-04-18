<?php

namespace SoulboxCron\Models\Entities;

class SbSysCliRuntimeError extends \Phalcon\Mvc\Model {

	public function initialize() {
		$this->setSource("sb_sys_cli_runtime_error");
	}
}
