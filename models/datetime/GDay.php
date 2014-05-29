<?php

namespace gear\models\datetime;
use gear\Core;
use gear\library\GModel;

class GDay extends GModel
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_datetime = null;
    protected $_value = null;
    /* Public */

    public function __toString() { return (string)$this->_value; }

    public function getDayOfWeek($type = 1)
    {
        return \gear\helpers\GDatetime::format((int)$type === 2 ? 'D' : 'l');
    }

    public function getDatetime()
    {
        return $this->_datetime;
    }
}
