<?php

namespace Gear\Library\Db;

use Gear\Library\GPlugin;
use Gear\Traits\TDbStorage;
use Gear\Traits\TDelegateFactory;
use Gear\Traits\TFactory;

/**
 * Бибилиотека для плагинов, работающих с данными в базе данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GDbStoragePlugin extends GPlugin implements \IteratorAggregate
{
    /* Traits */
    use TDbStorage;
    use TFactory;
    use TDelegateFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_factoryProperties = [
        'class' => '\Gear\Library\GModel',
    ];
    protected $_connection = null;
    protected $_connectionName = 'db';
    protected $_dbName = '';
    protected $_collectionName = '';
    protected $_defaultParams = [];
    /* Public */
}
