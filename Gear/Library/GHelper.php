<?php

namespace Gear\Library;

use Gear\Interfaces\IHelper;
use Gear\Traits\TGetter;
use Gear\Traits\THelper;
use Gear\Traits\TProperties;
use Gear\Traits\TSetter;

/**
 * Класс хелперов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GHelper implements IHelper
{
    /* Traits */
    use THelper;
    use TGetter;
    use TSetter;
    use TProperties;
    /* Const */
    /* Private */
    /* Protected */
    protected $_properties = [];
    /* Public */
}