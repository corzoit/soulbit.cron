<?php
namespace Utilities\Mail;

class SendgridWrapper
{

    private $config = null;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send($params, $add_bottom_padding = true)
    {
        $host = 'https://api.sendgrid.com';
        $endpoint = '/api/mail.send.json';
        //$endpoint = '/v3/mail.send.json'; //issues using v3

        if($this->config
            && property_exists($this->config, 'account')
            && property_exists($this->config, 'password'))
        {
            if(isset($params['from']) && strlen(trim($params['from'])) > 0
                && isset($params['to']) && strlen(trim($params['to'])) > 0
                && isset($params['subject']) && strlen(trim($params['subject'])) > 0
                && isset($params['message']) && strlen(trim($params['message'])) > 0)
            {
                $fromname           = isset($params['fromname']) && strlen(trim($params['fromname'])) > 0 
                                        ?   $params['fromname']:"";
                $toname             = isset($params['toname']) && strlen(trim($params['toname'])) > 0 
                                        ?   $params['toname']:"";
                $params['message']  = $add_bottom_padding ? $params['message'].'<br /><br />':$params['message'];

                $fields = array('api_user' => $this->config->account,
                                'api_key' => $this->config->password,
                                'from' => $params['from'],
                                'to' => $params['to'],
                                'subject' => $params['subject'],
                                'html' => $params['message']);
                if($fromname != '')
                {
                    $fields['fromname'] = $fromname;
                }

                if($toname != '')
                {
                    $fields['toname'] = $toname;
                }
                
                $auth_header = array('Authorization: Basic '.base64_encode($this->config->account.':'.$this->config->password));

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $host.$endpoint);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $auth_header);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));       
                               
                $response = curl_exec ($ch);

                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $header = substr($response, 0, $header_size);
                $body = substr($response, $header_size);

                curl_close ($ch);
                
                return json_encode($body);
            }
            else
            {
                throw new Exception("Class '".get_class($this)."': missing from/to/subject/message params (".__LINE__.")");            
            }                
        }
        else
        {
            throw new Exception("Class '".get_class($this)."': config is not properly set (".__LINE__.")");            
        }        
    }
}