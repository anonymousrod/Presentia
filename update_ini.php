<?php

$iniFile = 'C:\wamp64\bin\php\php8.4.1\php.ini';
$content = file_get_contents($iniFile);

$content = preg_replace('/^upload_max_filesize\s*=\s*.+$/m', 'upload_max_filesize = 128M', $content);
$content = preg_replace('/^post_max_size\s*=\s*.+$/m', 'post_max_size = 128M', $content);

file_put_contents($iniFile, $content);

echo "php.ini mis à jour avec succès.\n";
