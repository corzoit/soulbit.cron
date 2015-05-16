<?php

/**
 * Settings to be stored in dependency injector
 */

$__base_folder = substr(__FILE__, 0, strrpos(__FILE__, '/')+1).'../../';

$settings = array(
	'database' => array(
		'adapter' => 'Mysql',
		'host' => 'localhost',
		'username' => 'sbxworker',
		'password' => 'max1mum$PIDER',
		'name' => 'soulboxdb',
		'port' => 3306
	),
    'maildump' => array(
        'path' => $__base_folder.'sandbox/maildump/',
        'attachment_path' => '/tmp/mail-attachments/'),
    'logs' => array(
        'main' => '/tmp/app/logs/main.log'), /*$__base_folder.'app/logs/main.log')*/
    'reminder' => array(
        'secret' => 'SBXr3m!nd3r',
        'length' => 15,
        'alpha' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'),
    'mailer' => array(
        'default' => 'mandrill',
        'mandrill' => array(
            'account' => 'alexcorzo@gmail.com',
            'description' => 'Soulbox',
            'key' => 'o2P2sGc-JPF6UuXPiHyDaw',
            'password' => null,),
        'sendgrid' => array(
            'account' => 'corzoit',
            'description' => 'Soulbox',
            'key' => null,
            'password' => '12345678a'),)
);
return $settings;
