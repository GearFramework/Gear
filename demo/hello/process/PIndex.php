<?php

namespace demo\hello\process;

use gear\Core;
use gear\models\GProcess;

class PIndex extends GProcess
{
    public function apiIndex()
    {
        echo "Hello world!\n";
    }
}