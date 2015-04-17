<?php

/**
 * Mailer application (inbound)
 * 
 * @author Alex Corzo
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;

use \SoulboxCron\Models\Repositories\SbMessage as RepoMessage,

//http://stackoverflow.com/questions/6004453/how-to-remove-multiple-deleted-files-in-git-repository
class MailerTask extends \Phalcon\Cli\Task {

	public function processAction() {
        
        echo "\ncron to process!\n\n";
	}
}
