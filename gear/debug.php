<?php

$source = file('Core.php', FILE_IGNORE_NEW_LINES);
foreach($source as &$line) {
    if (preg_match('#^\/\*\*.*?\*\*\/$#', trim($line))) {
        $line = str_replace(['/**', '**/'], '', trim($line));
        echo $line . "\n";
        $line = prepare_log($line);
    }
}

file_put_contents('Core.php', implode("\n", $source));

function prepare_log($line) {
    $match = [];
    preg_match_all('/\{@(.*?)\}/', $line, $match);
    $s = '';
    foreach($match[0] as $i => $m) {
        $line = str_replace($match[0][$i], '%s', $line);
    }
    $s = 'sprintf(\'' . $line . '\', ' . implode(', ', $match[1])  . ')';
    $log = 'Core::syslog(' . $s . ');';
    echo $log . "\n";
    return $log;
}