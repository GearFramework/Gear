<?php

ini_set('display_errors', 1);

require_once '../Core.php';

function testConfig($config, $mode = \gear\Core::DEVELOPMENT, $short = false, $result = null, $exceptionResult = false): bool
{
    echo !$short ? str_repeat('=', 80) . "\nTest config:\n" . str_repeat('-', 80) . "\n" : "Test config: ";
    echo !$short ? "With params:\n" : '';
    echo !$short ? "\t\$config = " . str_replace("\n", '', var_export($config, true)) . "\n" : '';
    echo !$short ? "\t\$mode = " . var_export($mode, true) . "\n" : '';
    try {
        $r = \gear\Core::init($config, $mode);
        echo !$short ? "Result " . (!$exceptionResult || ($result !== null && $r === $result) ? "[OK]\n" : "[ERROR]\n") : '';
        $r = !$exceptionResult || ($result !== null && $r === $result);
    } catch(\Throwable $e) {
        echo !$short ? "Exception message: " . $e->getMessage() . " [" . get_class($e) . "]\n" : '';
        $r = $exceptionResult;
        echo !$short ? "Result " . ($exceptionResult ? "[OK]\n" : "[ERROR]\n") : '';
    }
    echo (!$short ? "Result "  : "") . ($r ? "[OK]\n" : "[ERROR]\n");
    return $r;
}

function testServicesRegister($name, $service, $type, $result = null)
{
    echo str_repeat('=', 80) . "\nRegister service:\n" . str_repeat('-', 80) . "\n";
    echo "With params:\n";
    echo "\t\$name = " . var_export($name, true) . "\n";
    echo "\t\$service = " . str_replace("\n", '', var_export($service, true)) . "\n";
    echo "\t\$type = " . var_export($type, true) . "\n";
    try {
        $r = \gear\Core::registerService($name, $service, $type);
        return true;
    } catch(\Throwable $e) {
        echo "Exception message: " . $e->getMessage() . " [" . get_class($e) . "]\n";
        return false;
    }
}

testConfig('', 1, true, null, true);
testConfig([], 1, true);
testConfig(function() { return []; }, 1, true);
testConfig(1, 1, true, null, true);

//echo "Result " . (testServicesRegister('test', ['class' => '\Test'], 'component') ? "[OK]\n" : "[ERROR]\n");
//echo "Result " . (testServicesRegister(function() { return 'Test1'; }, ['class' => '\Test'], 'component') ? "[OK]\n" : "[ERROR]\n");
//echo "Result " . (testServicesRegister('test1', function() { return ['class' => '\Test']; }, 'component') ? "[OK]\n" : "[ERROR]\n");
//echo "Result " . (testServicesRegister(function() { return []; }, function() { return ['class' => '\Test']; }, 'component') ? "[OK]\n" : "[ERROR]\n");
//echo "Result " . (testServicesRegister('test1', function() { return ''; }, 'component') ? "[OK]\n" : "[ERROR]\n");
