<?php

namespace gear\components\gear\curl;
use gear\library\GModel;

class GResponse extends GModel
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    public function __toString()
    {
        if ($this->error)
            return '#' . $this->error->number . ':' . $this->error->message;
        else
            return $this->return;
    }
}
