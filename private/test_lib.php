<?php
    require_once('../app/library/utilities/mail/MimeMailParser.class.php');

    $path = '../sandbox/maildump/1428945227_312.mail';
    $Parser = new MimeMailParser();
    $Parser->setPath($path);

    $to = $Parser->getHeader('to');
    $from = $Parser->getHeader('from');
    $subject = $Parser->getHeader('subject');
    $text = $Parser->getMessageBody('text');
    $html = $Parser->getMessageBody('html');
    $attachments = $Parser->getAttachments();

    echo("\n\nto1 = $to<br />");
    echo("\n\nfrom = $from<br />");
    echo("\n\nsubject = $subject<br />");
    echo("\n\ntext = $text<br />");
    echo("\n\nhtml = $html<br />");
    echo("\n\nattachments = $attachments<br />");

    $save_dir = '/tmp/mail-attachments/'; //saving files to tmp
    if(!is_dir($save_dir))
    {
        mkdir($save_dir, 0775);
    }
    
    foreach($attachments as $attachment) {
      // get the attachment name
      $filename = $attachment->filename;
      // write the file to the directory you want to save it in
      if ($fp = fopen($save_dir.$filename, 'w')) {
        while($bytes = $attachment->read()) {
          fwrite($fp, $bytes);
        }
        fclose($fp);

        echo("\n\n".$save_dir.$filename."<br />");
      }
    }    
