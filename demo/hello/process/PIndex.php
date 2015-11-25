<?php

namespace demo\hello\process;

use gear\Core;
use gear\models\GProcess;

class PIndex extends GProcess
{
    public function apiIndex()
    {
        echo "Hello World!"; // Output Hello World!
        Core::app()->out("Hello World!"); // Output Hello World!
        Core::app()->setOutputCallbacks(function($value) { return strtoupper($value); }); // Set output callback function
        Core::app()->out("Hello World!"); // Output HELLO WORLD!
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