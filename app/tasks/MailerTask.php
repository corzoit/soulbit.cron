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
use \Utilities\Mail\SendgridWrapper as SendgridWrapper;
use \Utilities\Mail\MandrillWrapper as MandrillWrapper;
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
            echo "Reminders created: ".$num_created;
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

            $mail_with = $this->config->mailer->default;
            $wrapper = null;

            if($mail_with == 'sendgrid')
            {
                $wrapper = new SendgridWrapper($this->config->mailer->sendgrid);
            }
            else if($mail_with == 'mandrill')
            {
                $wrapper = new MandrillWrapper($this->config->mailer->mandrill);
            }
           
            //TODO: process and send email
            $reminder_repo = new RepoReminder();
            $reminder_emails = $reminder_repo->getReminderEmails(1000);
            while($wrapper != null && count($reminder_emails))
            {
                foreach($reminder_emails as $key => $reminder_email)
                {
                    /**/
                    $send_params = array('fromname' => 'Soulbox Reminders',
                                            'from' => 'reminders@soulboxapp.com',
                                            'to' => $reminder_email->receiver_email, 
                                            'subject' => $reminder_email->subject.' ('.$reminder_email->pubid.')',
                                            'message' => $reminder_email->message);

                    $response = $wrapper->send($send_params);
                    $response_arr = json_decode($response, true);

                    if($mail_with == 'sendgrid')
                    {
                        //TODO
                    }
                    else if($mail_with == 'mandrill')
                    {
                        if(is_array($response_arr)
                            && isset($response_arr[0]['_id']))
                        {
                            $reminder_email->processed = 1;
                            $reminder_email->mailer = $mail_with;
                            $reminder_email->mailer_id = $response_arr[0]['_id'];
                        }
                        else //recording error
                        {
                            $reminder_email->processed = -1; //processed with error
                            $reminder_email->mailer = $mail_with;
                            $reminder_email->mailer_error = $response;
                        }

                        $reminder_repo->updateRemainderMailer($reminder_email);
                    }                    
                }

                $reminder_emails = $reminder_repo->getReminderEmails(1000);
            }
        }
        else
        {
            echo "\nTerminating process because it is running already\n";
            exit();
        }
    }    

    public function testAction()
    {
        $mail_with = $this->config->mailer->default;

        $send_params = array('fromname' => 'Soulbox Reminders',
                                'from' => 'reminders@soulboxapp.com',
                                'to' => 'alex.corzo@flexit.net',
                                'subject' => '['.$mail_with.'] No OB - Hello from Wrapper',
                                'message' => 'Hi Alex,<br /><br />
                                                This is a message sent from the wrapper!<br /><br />
                                                Bye!');

        $wrapper = null;

        if($mail_with == 'sendgrid')
        {
            $sendgrid_conf = $this->config->mailer->sendgrid;
            $wrapper = new SendgridWrapper($sendgrid_conf);
        }
        else if($mail_with == 'mandrill')
        {
            $mandrill_conf = $this->config->mailer->mandrill;
            $wrapper = new MandrillWrapper($mandrill_conf);
        }

        if($wrapper != null)
        {
            $response = $wrapper->send($send_params);
            echo "\n\nOut:\n";
            echo $response;
            echo "\n\nEND\n";
        }
    }    
}
