<?php

/**
 * Mailer application (inbound)
 * 
 * @author Alex Corzo
 * @version 1.0
 */

namespace Tasks;

use \Cli\Output as Output;

use \Models\Repositories\SbMessage as RepoMessage;
use \Utilities\Mail\MimeMailParser as SerMimeMail;

//http://stackoverflow.com/questions/6004453/how-to-remove-multiple-deleted-files-in-git-repository

class MailerTask extends \Phalcon\Cli\Task {
//class MailerTask{

	public function processAction() {
        
    $path = '../sandbox/maildump/1428945227_312.mail';

    $message = new RepoMessage();
    $Parser  = new SerMimeMail();

    echo "\n---------------------------------------\n";
    echo "\ncron to process 1!\n";
    echo "\n---------------------------------------\n";
    
    $Parser->setPath($path);
    $to = $Parser->getHeader('to');
    $from = $Parser->getHeader('from');
    $subject = $Parser->getHeader('subject');
    $text = $Parser->getMessageBody('text');
    $html = $Parser->getMessageBody('html');
    $attachments = $Parser->getAttachments();

    echo("\n to   = $to \n");
    echo("\n from = $from \n");
    echo("\n subject = $subject \n");
    echo("\n text = $text \n");
    echo("\n html = $html \n");
    echo("\n attachments = $attachments");

    $save_dir = '/tmp/mail-attachments/'; //saving files to tmp
    if(!is_dir($save_dir))
    {
        mkdir($save_dir, 0775);
    }
    
    foreach($attachments as $attachment)
    {
      // get the attachment name
      $filename = $attachment->filename;
      // write the file to the directory you want to save it in
      if ($fp = fopen($save_dir.$filename, 'w')) {
        while($bytes = $attachment->read()) {
          fwrite($fp, $bytes);
        }
        fclose($fp);

        echo("\n\n".$save_dir.$filename."\n");
      }
    }

    echo "\n---------------------------------------\n";
	}
}
