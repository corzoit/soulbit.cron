<?php

/**
 * Mailer application (inbound)
 * 
 * @author Alex Corzo
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;


class MailerTask extends \Phalcon\Cli\Task {

	public function readAction() {
        echo "\ncron to process!\n\n";
	}
}
