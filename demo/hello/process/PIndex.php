<?php

namespace demo\hello\process;

use gear\Core;
use gear\library\GProcess;

class PIndex extends GProcess
{
    public function apiIndex()
    {
        Core::app()->setOutputCallbacks(function($value) { return "$value\n"; });
        $now = \Calendar::now();
        Core::app()->out('Yesterday ' . $tomorrow = $now->yesterday());
        Core::app()->out('Now ' . $now);
        Core::app()->out('Tomorrow ' . $tomorrow = $now->tomorrow());
        Core::app()->out('Add 1 day ' . $now->now()->addDay());
        $count = rand(2, 6);
        Core::app()->out('Add ' . $count . ' days ' . $now->addDays($count));
        Core::app()->out('Sub 1 day ' . $now->subDay());
        $count = rand(2, 6);
        Core::app()->out('Sub ' . $count . ' days ' . $now->subDays($count));
        $count = rand(2, 6);
        Core::app()->out('Add ' . $count . ' months ' . $now->addMonths($count));
        $count = rand(2, 6);
        Core::app()->out('Sub ' . $count . ' months ' . $now->subMonths($count));
        $count = rand(2, 6);
        Core::app()->out('Add ' . $count . ' years ' . $now->addYears($count));
        $count = rand(2, 6);
        Core::app()->out('Sub ' . $count . ' years ' . $now->subYears($count));
        $now->setDay(12);
        Core::app()->out('Set day 12 ' . $now);
        $now->setMonth(11);
        Core::app()->out('Set month 11 ' . $now);
        $now->setYear(2011);
        Core::app()->out('Set year 11 ' . $now);
        $now->setHour(23);
        Core::app()->out('Set hour 23 ' . $now);
        $now->setMinute(23);
        Core::app()->out('Set minute 23 ' . $now);
        $now->setSecond(23);
        Core::app()->out('Set second 23 ' . $now);
        //echo "Hello World!"; // Output Hello World!
        //Core::app()->out("Hello World!"); // Output Hello World!
        //Core::app()->setOutputCallbacks(function($value) { return strtoupper($value); }); // Set output callback function
        //Core::app()->out("Hello World!"); // Output HELLO WORLD!
        return true;
    }

    public function apiView()
    {
        $this->view('index'); // Output <b>Hello World!</b> (path resolved as demo/hello/views/index.phtml)
        $this->view('views/index'); // Output <b>Hello World!</b> (path resolved as demo/hello/views/index.phtml)
        $this->view($this->viewPath . '/index'); // Output <b>Hello World!</b> (path resolved as demo/hello/views/index.phtml)
        return true;
    }
}
