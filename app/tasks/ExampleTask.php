<?php

/**
 * Example of a Task/Cli application
 * 
 * @author Jete O'Keeffe
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;


class ExampleTask extends \Phalcon\Cli\Task {

	public function test1Action() {
        $fi = fopen("php://stdin", "r");
        $contents = "";
        while(!feof($fi))
        {
            Output::stdout("\nreading line");
            
            $contents .= fread($fi, 1024);
        }
        fclose($fi);

        $f = "/tmp/".time();
        file_put_contents($f, $contents);
        
		Output::stdout("\nDone: ".$f);
	}

	public function mainAction() {
		Output::stdout("Main Action");
	}


	public function cmdAction() {
		$cmd = \Cli\Execute::singleton();
		$success = $cmd->execute("whoami", __FILE__, __LINE__, $output);

		Output::stdout("You're running this script under $output user");
	}


	public function test2Action($paramArray) {
		Output::stdout("First param: $paramArray[0]");
		Output::stdout("Second param: $paramArray[1]");
	}


	/**
	 * Action to trigger a fatal error
	 */
	public function fatalAction() {
		// trigger a fatal error w/ Class that doesn't exist
		//new TriggerARuntimeFatal();
		strpos();
	}
}