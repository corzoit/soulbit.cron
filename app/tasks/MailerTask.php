<?php

/**
 * Mailer application (inbound)
 * 
 * @author Alex Corzo
 * @version 1.0
 */

namespace Tasks;

use \Phalcon\Logger\Adapter\File as FileAdapter;

use \Cli\Output as Output;

use \SoulboxCron\Models\Repositories\SbReminder as RepoReminder;
use \SoulboxCron\Models\Repositories\SbMessage as RepoMessage;

use \Utilities\Mail\MimeMailParser as MailParser;
use \Utilities\Cron\QueueMaster as QueueMaster;

class MailerTask extends \Phalcon\Cli\Task
{

    /*
     * Processes mails sent and replies to add them to the database
     */
	public function processAction()
    {
        $qm = new QueueMaster('cli.php Mailer process');
        $pid = $qm->getPid();

        if(!$qm->isRunning()) //only if process is not running already
        {
            $qm->updatePid();
            
            //functionality here

            //TODO: Differenciate between regular emails and replies to emails sent as reminders

            $log_file = $this->config->logs->main;
            $log_file_path = substr($log_file, 0, strrpos($log_file, "/"));
            if(!is_dir($log_file_path))
            {
                mkdir($log_file_path, 0775, true);
            }
            if(!file_exists($log_file))
            {
                touch($log_file);
            }

            $logger = new FileAdapter($log_file);
            
            $maildump_path = $this->config->maildump->path;
            $attachment_path = $this->config->maildump->attachment_path;

            if(!is_dir($maildump_path))
            {
                $logger->error("Maildump path doesn't exist: ".$maildump_path);
                exit();
            }

            if ($handle = opendir($maildump_path))
            {
                //$message = new RepoMessage();
                $parser  = new MailParser();

                while (false !== ($entry = readdir($handle)))
                {
                    if ($entry != "." && $entry != "..")
                    {
                        $parser->setPath($maildump_path.$entry);

                        $to         = $parser->getHeader('to');
                        $from       = $parser->getHeader('from');
                        $subject    = $parser->getHeader('subject');
                        $text       = $parser->getMessageBody('text');
                        $html       = $parser->getMessageBody('html');
                        $attachments = $parser->getAttachments();

                        
                        if(!is_dir($attachment_path))
                        {
                            mkdir($attachment_path, 0775);
                        }

                        foreach($attachments as $attachment)
                        {
                            // get the attachment name
                            $filename = $attachment->filename;
                            // write the file to the directory you want to save it in
                            if ($fp = fopen($attachment_path.$filename, 'w'))
                            {
                                while($bytes = $attachment->read())
                                {
                                    fwrite($fp, $bytes);
                                }
                                fclose($fp);

                                echo("\n\n".$attachment_path.$filename."\n");
                            }
                        }

                        $logger->log("Processed: ".$maildump_path.$entry);
                    }
                }
                closedir($handle);
            }
        }
        else
        {
            echo("\nTerminating process because it is running already\n");
            exit();
        }
	}

    /*
     * Creates the DB to later send the email reminders
     */
    public function reminderCreateAction()
    {
        $qm = new QueueMaster('cli.php Mailer reminderCreate');
        $pid = $qm->getPid();

        if(!$qm->isRunning()) //only if process is not running already
        {
            $qm->updatePid();
            
            //functionality here
            $reminder_repo = new RepoReminder();
            $reminders = $reminder_repo->getRemindersByFrequency();
            $num_created = $reminder_repo->createReminderEmails($reminders, $this->config->reminder);
            echo("Reminders created: ".$num_created);
        }
        else
        {
            echo("\nTerminating process because it is running already\n");
            exit();
        }
    }

    /*
     * Send the reminder emails
     */
    public function reminderSendAction()
    {
        $qm = new QueueMaster('cli.php Mailer reminderSend');
        $pid = $qm->getPid();

        if(!$qm->isRunning()) //only if process is not running already
        {
            $qm->updatePid();
            
            //TODO: process and send email
            $reminder_repo = new RepoReminder();
            $reminder_emails = $reminder_repo->getReminderEmails(1000);
            while(count($reminder_emails))
            {
                foreach($reminder_emails as $key => $reminder_email)
                {
                    /*HERE*/
                }

                $reminder_emails = $reminder_repo->getReminderEmails(1000);
            }
        }
        else
        {
            echo("\nTerminating process because it is running already\n");
            exit();
        }
    }    

    public function testAction()
    {

        $sendgrid_conf = $this->config->mailer->sendgrid;

/*
curl -X POST https://api.sendgrid.com/api/mail.send.json -d api_user=alexcorzo@gmail.com -d api_key=XXXXXXXX -d 
to=alexcorzo@gmail.com -d toname=Alex GMAIL -d subject=hey how is it going -d text=huh? -d html=huh? HTML -d from=alex.corzo@flexit.net
*/

        $fields = array('api_user' => $sendgrid_conf->account,
                        'api_key' => $sendgrid_conf->password,
                        'from' => 'alex.corzo@flexit.net',
                        'to' => 'alexcorzo@gmail.com',
                        'toname' => 'ACDC',
                        'subject' => 'VER 1111! sendgrid test using curl',
                        'html' => 'html VERSION 111 - version 3<br /><br /><br />');

        $ch = curl_init();

        //curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/api/mail.send.json'); //works
        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail.send.json'); //doesn't
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        
        $response = curl_exec ($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close ($ch);

        echo("\n\n============\n\n");
        print_r (array('status_code' => $http_code,
                        'headers' => $header,
                        'body' => $body,
                        'json' => ''));
        echo("\n\n============\n\n");





/*
        echo("\n\n*1*1\n\n");

        $sendgrid = new \SendGrid\SendGrid($sendgrid_conf->account, $sendgrid_conf->password);
        
        $email = new \SendGrid\Email();
        $email->addTo('alex.corzo@flexit.net')
            ->setFrom('alexcorzo@gmail.com')
            ->setSubject('Testing SG integration')
            ->setText('Hello World!')
            ->setHtml('<strong>Hello World!</strong>');

        $res = $sendgrid->send($email);

        var_dump($res);
        
        echo("\n\nSENDGRID OBJ CREATED\n\n");        
*/
        /*
    'mailer' => array(
        'default' => 'sendgrid',
        'mandrill' => array(
            'account' => 'alexcorzo@gmail.com',
            'description' => 'Soulbox',
            'key' => 'o2P2sGc-JPF6UuXPiHyDaw',
            'password' => null,),
        'sendgrid' => array(
            'account' => 'alexcorzo@gmail.com',
            'description' => 'Soulbox',
            'key' => null,
            'password' => '7877855574'),)
);
        */
    }    
}
