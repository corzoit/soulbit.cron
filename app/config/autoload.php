<?php

/**
 * Autoload Class files by PHP namespacing support
 * 
 * @author Jete O'Keeffe
 * @eg 
 *	$autoload = [
 *		'namespace' => '/path/to/dir',
 *		'namespace' => '/path/to/dir'
 *	];
 *	return $autoload;
 *
 * @note $appDir is the app directory (/path/to/php-cli-app-phalcon/app)
 */

$autoload = [
	'Utilities\Debug' => $appDir . '/library/utilities/debug/',
	'Utilities\Mail' => $appDir . '/library/utilities/mail',
	'Application' => $appDir . '/library/application/',
	'Interfaces' => $appDir . '/library/interfaces/',
	'Controllers' => $appDir . '/controllers/',
	'Models\Repositories' => $appDir . '/models/repositories',
	'SoulboxCron\Models\Entities' => $appDir . '/models/entities',
	'Tasks' => $appDir . '/tasks/',
	'Cli' => $appDir . '/library/cli/',
	'Events\Cli' => $appDir . '/library/events/cli/',
	'Events\Database' => $appDir . '/library/events/database/',
];

return $autoload;
