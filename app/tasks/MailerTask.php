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

use \SoulboxCron\Models\Repositories\SbMember as RepoMember;
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
            $member_repo = new RepoMember();
            $reminders = $member_repo->getRemindersByFrequency();
            $num_created = $member_repo->createReminderEmails($reminders, $this->config->reminder);
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
        }
        else
        {
            echo("\nTerminating process because it is running already\n");
            exit();
        }
    }    
}
