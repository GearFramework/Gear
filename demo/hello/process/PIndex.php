<?php

namespace demo\hello\process;

use gear\Core;
use gear\models\GProcess;

class PIndex extends GProcess
{
    public function apiIndex()
    {
        echo "Hello World!\n";
        return true;
    }

    public function apiView()
    {
        $this->view('index');
        return true;
    }
}