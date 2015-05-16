<?php
namespace Utilities\Mail;

class MandrillWrapper
{

    private $config = null;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send($params)
    {
        $host = 'https://mandrillapp.com/api/1.0';
        $endpoint = '/messages/send.json';

/*
{
    "key": "example key",
    "message": {
        "html": "<p>Example HTML content</p>",
        "text": "Example text content",
        "subject": "example subject",
        "from_email": "message.from_email@example.com",
        "from_name": "Example Name",
        "to": [
            {
                "email": "recipient.email@example.com",
                "name": "Recipient Name",
                "type": "to"
            }
        ],
        "headers": {
            "Reply-To": "message.reply@example.com"
        },
        "important": false,
        "track_opens": null,
        "track_clicks": null,
        "auto_text": null,
        "auto_html": null,
        "inline_css": null,
        "url_strip_qs": null,
        "preserve_recipients": null,
        "view_content_link": null,
        "bcc_address": "message.bcc_address@example.com",
        "tracking_domain": null,
        "signing_domain": null,
        "return_path_domain": null,
        "merge": true,
        "merge_language": "mailchimp",
        "global_merge_vars": [
            {
                "name": "merge1",
                "content": "merge1 content"
            }
        ],
        "merge_vars": [
            {
                "rcpt": "recipient.email@example.com",
                "vars": [
                    {
                        "name": "merge2",
                        "content": "merge2 content"
                    }
                ]
            }
        ],
        "tags": [
            "password-resets"
        ],
        "subaccount": "customer-123",
        "google_analytics_domains": [
            "example.com"
        ],
        "google_analytics_campaign": "message.from_email@example.com",
        "metadata": {
            "website": "www.example.com"
        },
        "recipient_metadata": [
            {
                "rcpt": "recipient.email@example.com",
                "values": {
                    "user_id": 123456
                }
            }
        ],
        "attachments": [
            {
                "type": "text/plain",
                "name": "myfile.txt",
                "content": "ZXhhbXBsZSBmaWxl"
            }
        ],
        "images": [
            {
                "type": "image/png",
                "name": "IMAGECID",
                "content": "ZXhhbXBsZSBmaWxl"
            }
        ]
    },
    "async": false,
    "ip_pool": "Main Pool",
    "send_at": "example send_at"
}
*/

        if($this->config
            && property_exists($this->config, 'key'))
        {
            if(isset($params['from']) && strlen(trim($params['from'])) > 0
                && isset($params['to']) && strlen(trim($params['to'])) > 0
                && isset($params['subject']) && strlen(trim($params['subject'])) > 0
                && isset($params['message']) && strlen(trim($params['message'])) > 0)
            {
                $important          = isset($params['important']) && $params['from'] == true;
                $fromname           = isset($params['fromname']) && strlen(trim($params['fromname'])) > 0 
                                        ?   $params['fromname']:"";
                $toname             = isset($params['toname']) && strlen(trim($params['toname'])) > 0 
                                        ?   $params['toname']:"";

                $fields = array('key' => $this->config->key,
                                'message' => array('html' => $params['message'],
                                                    'text' => strip_tags(str_replace(array("<br>", "<br />", "<br/>"), "\n", $params['message'])),
                                                    'subject' => $params['subject'],
                                                    'from_email' => $params['from'],
                                                    'to' => array(
                                                                array('email' => $params['to'],
                                                                        'type' => 'to')),
                                                    'headers' => array('Reply-To' => $params['from']),
                                                    'important' => $important),
                                'async' => false,

                                );

                if($fromname != '')
                {
                    $fields['message']['from_name'] = $fromname;
                }

                if($toname != '')
                {
                    $fields['message']['to'][0]['name'] = $toname;
                }
                
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $host.$endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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