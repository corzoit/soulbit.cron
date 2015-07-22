<?php

namespace SoulbitCron\Models\Repositories;

use \Utilities\Crypt\Hashids as Hashids;

use \SoulbitCron\Models\Entities\SbMessage as EntityMessage;

class SbMessage
{
    public function __construct()
    {
        
    }

    public function saveFromFile($path)
    {
        if(file_exists($path))
        {
            
        }
    }

    public function createMessage($message_data, $message_config)
    {
        $messages_created = 0;

        $messaje_obj = new EntityMessage();
        $messaje_obj->loadFromArray($message_data);
        if($messaje_obj->save())
        {
            $hashids = new Hashids($message_config->secret, 
                                    $message_config->length, 
                                    $message_config->alpha);
            $messaje_obj->pubid = $hashids->encode($messaje_obj->sb_message_id);
            $messaje_obj->save();
                    
            $messages_created++;
        }
        else
        {
            foreach ($messaje_obj->getMessages() as $message)
            {
                echo $message, "\n";
            }
            exit();
        }   

        return $messages_created > 0 ? $messaje_obj:false;
    }
}