<?php

namespace SoulboxCron\Models\Repositories;

use \Utilities\Crypt\Hashids as Hashids;

use \SoulboxCron\Models\Entities\SbMember as EntityMember;
use \SoulboxCron\Models\Entities\SbReminder as EntityReminder;
use \SoulboxCron\Models\Entities\SbReminderEmail as EntityReminderEmail;

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

        //$time_now       = date('H:i:').'00'; //utc time
        $time_now       = '18:31:00'; //for testing

        $conditions = "status = 1 
                        AND CHAR_LENGTH(TRIM(subject)) > 0
                        AND frequency_time = ?1
                        AND (frequency_t = 'd' 
                            OR (frequency_t = 'w' AND CONCAT(',', frequency_t, ',') LIKE ?2)
                            OR (frequency_t = 'm' AND CONCAT(',', frequency_t, ',') LIKE ?3)
                            OR (frequency_t = 'y' AND CONCAT(',', frequency_t, ',') LIKE ?4)
                        )";

        $reminders = EntityReminder::find(
            array(  'conditions' => $conditions,
                    'bind'       => array(1 => $time_now,
                                          2 => $day_of_week,
                                          3 => $day_of_month,
                                          4 => $day_of_year)
        ));

        return $reminders;
    }

    public function createReminderEmails($reminders, $reminder_config)
    {
        $reminders_created = 0;

        $date_now = date('Y-m-d');
        foreach($reminders as $key => $reminder)
        {
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
}