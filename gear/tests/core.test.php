<?php

ini_set('display_errors', 1);

require_once '../Core.php';

function testConfig($config, $mode = \gear\Core::DEVELOPMENT): bool
{
    echo str_repeat('=', 80) . "\nTest config:\n" . str_repeat('-', 80) . "\n";
    echo "With params:\n";
    echo "\t\$config = " . str_replace("\n", '', var_export($config, true)) . "\n";
    echo "\t\$mode = " . var_export($mode, true) . "\n";
    try {
        \gear\Core::init($config, $mode);
        return true;
    } catch(\Throwable $e) {
        echo "Exception message: " . $e->getMessage() . " [" . get_class($e) . "]\n";
        return false;
    }
}

function testServicesRegister($name, $service, $type)
{
    echo str_repeat('=', 80) . "\nRegister service:\n" . str_repeat('-', 80) . "\n";
    echo "With params:\n";
    echo "\t\$name = " . var_export($name, true) . "\n";
    echo "\t\$service = " . str_replace("\n", '', var_export($service, true)) . "\n";
    echo "\t\$type = " . var_export($type, true) . "\n";
    try {
        \gear\Core::registerService($name, $service, $type);
        return true;
    } catch(\Throwable $e) {
        echo "Exception message: " . $e->getMessage() . " [" . get_class($e) . "]\n";
        return false;
    }
}

echo "Result " . (testConfig('') ? "[OK]\n" : "[ERROR]\n");
echo "Result " . (testConfig([]) ? "[OK]\n" : "[ERROR]\n");
echo "Result " . (testConfig(function() { return []; }) ? "[OK]\n" : "[ERROR]\n");
echo "Result " . (testConfig(1) ? "[OK]\n" : "[ERROR]\n");

echo "Result " . (testServicesRegister('test', ['class' => '\Test'], 'component') ? "[OK]\n" : "[ERROR]\n");
echo "Result " . (testServicesRegister(function() { return 'Test1'; }, ['class' => '\Test'], 'component') ? "[OK]\n" : "[ERROR]\n");
echo "Result " . (testServicesRegister('test1', function() { return ['class' => '\Test']; }, 'component') ? "[OK]\n" : "[ERROR]\n");
echo "Result " . (testServicesRegister(function() { return []; }, function() { return ['class' => '\Test']; }, 'component') ? "[OK]\n" : "[ERROR]\n");
echo "Result " . (testServicesRegister('test1', function() { return ''; }, 'component') ? "[OK]\n" : "[ERROR]\n");
