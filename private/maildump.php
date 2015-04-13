#!/usr/bin/php
<?php

    $fi = fopen("php://stdin", "r");
    $contents = "";
    while(!feof($fi))
    {       
        $contents .= fread($fi, 1024);
    }
    fclose($fi);

    $f = "/tmp/".time().".mail";
    file_put_contents($f, $contents);