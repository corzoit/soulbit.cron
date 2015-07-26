<?php

/**
 * Settings to be stored in dependency injector
 */

$__base_folder = substr(__FILE__, 0, strrpos(__FILE__, '/')+1).'../../';

$settings = array(
	'database' => array(
		'adapter' => 'Mysql',
		'host' => 'alpha-soulboxdb.c8ku8altey84.us-west-2.rds.amazonaws.com',
		'username' => 'sbxworker',
		'password' => 'max1mum$PIDER',
		'name' => 'soulbitdb',
		'port' => 3306
	),
    'maildump' => array(
        /*'path' => $__base_folder.'sandbox/maildump/',*/
        'path' => '/tmp/maildump/',
        'attachment_path' => '/tmp/mail-attachments/'),
    'logs' => array(
        'main' => '/tmp/app/logs/main.log'), /*$__base_folder.'app/logs/main.log')*/
    'reminder' => array(
        'secret' => 'SBXr3m!nd3r',
        'algo' => 'md5',
        'email_from' => 'reminders@soulboxapp.com',
        'email_reply' => 'reminders+%pubid%@soulboxapp.com',),
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
