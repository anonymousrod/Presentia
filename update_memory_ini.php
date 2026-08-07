<?php

$iniFile = 'C:\wamp64\bin\php\php8.4.1\php.ini';
$content = file_get_contents($iniFile);

$content = preg_replace('/^memory_limit\s*=\s*.+$/m', 'memory_limit = 1024M', $content);

file_put_contents($iniFile, $content);

echo "php.ini memory_limit mis à jour avec succès.\n";
