<?php

namespace SoulbitCron\Models\Repositories;

use \Utilities\Crypt\Hashids as Hashids;

use \SoulbitCron\Models\Entities\SbMember as EntityMember;
use \SoulbitCron\Models\Entities\SbReminder as EntityReminder;
use \SoulbitCron\Models\Entities\SbReminderEmail as EntityReminderEmail;

class SbReminder
{
    public function __construct()
    {
        
    }

    public function getRemindersByFrequency()
    {
        $day_of_week    = date('N');
        $day_of_month   = date('j');
        $day_of_year    = date('z')+1;

        $time_now       = date('H:i:').'00'; //utc time
        $time_now       = '21:15:00'; //for testing, this has to match the record on DB for the process to go on

        $conditions = "status = 1 
                        AND CHAR_LENGTH(TRIM(subject)) > 0
                        AND frequency_time = ?1
                        AND (frequency_t = 'd' 
                            OR (frequency_t = 'w' AND CONCAT(',', frequency_v, ',') LIKE ?2)
                            OR (frequency_t = 'm' AND CONCAT(',', frequency_v, ',') LIKE ?3)
                            OR (frequency_t = 'y' AND CONCAT(',', frequency_v, ',') LIKE ?4)
                        )";

        $arr = array(1 => $time_now,
                      2 => ','.$day_of_week.',',
                      3 => ','.$day_of_month.',',
                      4 => ','.$day_of_year.',');

        $reminders = EntityReminder::find(
            array(  'conditions' => $conditions,
                    'bind'       => $arr
        ));

        return $reminders;
    }

    public function createReminderEmails($reminders, $reminder_config)
    {
        $reminders_created = 0;

        echo("count : ".count($reminders));

        $date_now = date('Y-m-d');
        foreach($reminders as $key => $reminder)
        {
            echo("\nkey = ".$key);
            $reminder_arr = $reminder->toArray();
            print_r($reminder_arr);

            $re_obj = new EntityReminderEmail();
            $re_obj->sb_reminder_id = $reminder->sb_reminder_id;
            $re_obj->sb_message_id  = null;
            $re_obj->receiver_email = $reminder->SbMember->email;
            $re_obj->subject        = $reminder->subject;
            $re_obj->message        = $reminder->message;
            $re_obj->sent_dt        = $date_now.' '.$reminder->frequency_time;
            $re_obj->processed      = 0;            

            if($re_obj->save())
            {
                $hashids = new Hashids($reminder_config->secret, 
                                        $reminder_config->length, 
                                        $reminder_config->alpha);
                $re_obj->pubid = $hashids->encode($re_obj->sb_reminder_email_id);
                $re_obj->save();
                        
                $reminders_created++;
            }
            else
            {
                foreach ($re_obj->getMessages() as $message)
                {
                    echo $message, "\n";
                }
                exit();
            }
        }

        return $reminders_created;
    }

    public function getReminderEmails($limit = 1000)
    {
        $conditions = "processed = 0
                        AND CHAR_LENGTH(TRIM(pubid)) > 0
                        AND sent_dt <= '".date('Y-m-d H:i:s')."'";

        $reminder_emails = EntityReminderEmail::find(array('conditions' => $conditions,
                                                            'limit' => $limit));
        return $reminder_emails;
    }

    public function updateRemainderMailer($reminder_email_obj)
    {
        $reminder_email_obj->save();
    }

    public function getReminderEmailByPubid($pubid)
    {
        $conditions = "processed = 1 AND pubid = ?1";

        $reminder_email = EntityReminderEmail::findFirst(array('conditions' => $conditions,
                                                                'bind' => array(1 => $pubid)));
        return $reminder_email;
    }
}