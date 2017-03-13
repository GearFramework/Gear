<?php

namespace gear\modules\user\identifies;

use gear\library\GModel;

abstract class GIdentity extends GModel
{
    abstract public function check();
}