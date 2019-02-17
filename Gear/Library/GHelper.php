<?php

namespace Gear\Library;

use Gear\Interfaces\HelperInterface;
use Gear\Traits\GetterTrait;
use Gear\Traits\HelperTrait;
use Gear\Traits\PropertiesTrait;
use Gear\Traits\SetterTrait;

/**
 * Класс хелперов
 *
 * @package Gear Framework
 *
 * @property iterable properties
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GHelper implements HelperInterface
{
    /* Traits */
    use HelperTrait;
    use GetterTrait;
    use SetterTrait;
    use PropertiesTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_properties = [];
    /* Public */
}
