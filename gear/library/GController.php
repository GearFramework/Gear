<?php

namespace gear\library;

use gear\interfaces\IController;
use gear\interfaces\IRequest;

class GController extends GModel implements IController
{

    public function __invoke(IRequest $request = null)
    {
        // TODO: Implement __invoke() method.
    }

    public function exec(IRequest $request = null)
    {
        // TODO: Implement exec() method.
    }
}