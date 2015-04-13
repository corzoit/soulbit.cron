#!/usr/bin/php
<?php
    $md_dir = "/tmp/maildump";
    if(!is_dir($md_dir))
    {
        mkdir($md_dir, 0775);
    }

    $fi = fopen("php://stdin", "r");
    $contents = "";
    while(!feof($fi))
    {       
        $contents .= fread($fi, 1024);
    }
    fclose($fi);

    $f = $md_dir."/".time()."_".mt_rand(100, 999).".mail";
    file_put_contents($f, $contents);