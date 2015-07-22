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

use \SoulbitCron\Models\Repositories\SbReminder as RepoReminder;
use \SoulbitCron\Models\Repositories\SbMessage as RepoMessage;

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

        //TODO: for some reason this doesn't work the first time you call this task, the second it works, there is a problem with the queue master logic
        if(!$qm->isRunning()) //only if process is not running already
        {
            $qm->updatePid();
            
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
                $reminder_repo = new RepoReminder();
                $message_repo = new RepoMessage();

                $parser  = new MailParser();

                while (false !== ($entry = readdir($handle)))
                {
                    if ($entry != "." && $entry != "..")
                    {
                        $remove_file = false;

                        $parser->setPath($maildump_path.$entry);

                        echo "\nFile parsing: ".$maildump_path.$entry."\n";

                        $to             = $parser->getHeader('to');
                        $from           = $parser->getHeader('from');
                        $subject        = $parser->getHeader('subject');
                        $text           = $parser->getMessageBody('text');
                        $html           = $parser->getMessageBody('html');
                        $attachments    = $parser->getAttachments();

                        echo "\nSubject: ".$subject." / $to\n";

                        
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

                                echo "\n\n".$attachment_path.$filename."\n";
                            }
                        }

                        $logger->log("Processed: ".$maildump_path.$entry);

                        if($to == "reminders@sbx.email") //reminder system
                        {
                            $bracket_pos = strrpos($subject, "(");
                            if($bracket_pos !== FALSE)
                            {
                                $pubid = substr($subject, $bracket_pos+1);
                                $pubid = substr($pubid, 0, strlen($pubid)-1);

                                $subject = substr($subject, 0, $bracket_pos);

                                //removing "Re: " from subject if exists. This is only a feature of the reminders
                                $subject = stripos($subject, "re: ") === 0 ? substr($subject, 4):$subject;

                                $reminder_email_obj = $reminder_repo->getReminderEmailByPubid($pubid);

                                echo "\nBACKET FOUND *$pubid*\n";
                                
                                //reminder found with the pubid captured on the subject line
                                if(is_object($reminder_email_obj)
                                    && property_exists($reminder_email_obj, 'sb_reminder_email_id')
                                    && is_numeric($reminder_email_obj->sb_reminder_email_id))
                                {
                                    echo "\nPROP FOUND\n";
                                    //now we make sure that this reminder does not have a "message" already, if it does we terminate this part of the process
                                    if(!is_numeric($reminder_email_obj->sb_message_id))
                                    {
                                        echo "\nNO MESSAGE ID | ".$reminder_email_obj->receiver_email." == ".$from."\n";
                                        //now we find out info about the sender, we will only accept a message from the original recipient of the reminder, nobody else
                                        if(strtolower($reminder_email_obj->receiver_email) == strtolower($from))
                                        {                                            
                                            $now_utc = date('Y-m-d H:i:s');

                                            //TODO: implement logic to clean part of the "message" so it doesn't contain the original message
                                            
                                            //we create a messaje Object
                                            $message_data = array('creation_dt' => $now_utc,
                                                                    'sb_sender_member_id' => $reminder_email_obj->SbReminder->sb_member_id,
                                                                    'mailer' => 'reminders',
                                                                    'message_type' => 'email',
                                                                    'subject' => $subject,
                                                                    'message' => $html,
                                                                    'summary' => '',
                                                                    'delivery_type' => 'set-date',
                                                                    'delivery_dt' => $now_utc,
                                                                    'delivery_age' => 0,
                                                                    'delivery_age_day_offset' => 0);
                                            
                                            $message_obj = $message_repo->createMessage($message_data, $this->config->message);

                                            if(is_object($message_obj))
                                            {
                                                $reminder_email_obj->sb_message_id = $message_obj->sb_message_id;
                                                $reminder_repo->updateRemainderMailer($reminder_email_obj);

                                                //TODO: implement receiver logic URGENT
                                                //TODO: implement attachment logic URGENT

                                                echo "\nMessage created @ ".$now_utc."\n";

                                                $remove_file = true;
                                            }
                                            else
                                            {
                                                echo "\n---0\n";
                                                //TODO: implement logging logic for stats                                                
                                            }
                                        }
                                        else
                                        {
                                            echo "\n---1\n";
                                            //TODO: implement logging logic for stats
                                        }
                                    }
                                    else
                                    {
                                        echo "\n---2\n";
                                        //TODO: implement logging logic for stats
                                    }                                    
                                }
                                else
                                {
                                    echo "\n---3\n";
                                    //TODO: implement logging logic for stats
                                }                                
                            }
                            else
                            {
                                echo "\n---4\n";
                                //TODO: implement logging logic for stats
                            }                            
                            
                        }
                        else //mail to self or to an authorised user, need to check
                        {
                            echo "\n---5\n";
                            //TODO: implement this flow where a user sends an email to an @sbx.email box which translates 
                            //      as a private message to an existing user or to self. If it is to another user the system
                            //      needs to validate that the sender has access (connection) to the receiver
                        }

                        if($remove_file)
                        {
                            unlink($maildump_path.$entry);
                        }
                    }
                }
                closedir($handle);
            }
        }
        else
        {
            echo "\nTerminating process because it is running already\n";
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
            echo "\nReminders created: ".$num_created."\n";
        }
        else
        {
            echo"\nTerminating process because it is running already\n";
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
           
            $reminder_repo = new RepoReminder();
            $reminder_emails = $reminder_repo->getReminderEmails(1000);
            //TODO: improvement, if using Mandrill then send chunks of 1000
            while($wrapper != null && count($reminder_emails))
            {
                foreach($reminder_emails as $key => $reminder_email)
                {
                    $send_params = array('fromname' => 'Soulbox Reminders',
                                            'from' => 'reminders@sbx.email',
                                            'to' => $reminder_email->receiver_email, 
                                            'subject' => $reminder_email->subject.' ('.$reminder_email->pubid.')',
                                            'message' => $reminder_email->message);

                    $response = $wrapper->send($send_params);
                    $response_arr = json_decode($response, true);

                    if($mail_with == 'sendgrid')
                    {
                        //TODO: implement sendgrid response logic
                    }
                    else if($mail_with == 'mandrill')
                    {
                        if(is_array($response_arr)
                            && isset($response_arr[0]['_id']))
                        {
                            $reminder_email->processed = 1;
                            $reminder_email->mailer = $mail_with.'"and';
                            $reminder_email->mailer_id = $response_arr[0]['_id'];
                            $reminder_email->mailer_error = "";
                        }
                        else //recording error
                        {
                            $reminder_email->processed = -1; //processed with error
                            $reminder_email->mailer = $mail_with;
                            $reminder_email->mailer_id = null;
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
                                'from' => 'reminders@sbx.email',
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
