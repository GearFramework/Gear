<?php

namespace Gear\Library\Db;

use Gear\Interfaces\IFactory;
use Gear\Library\GComponent;
use Gear\Traits\TDbStorage;
use Gear\Traits\TDelegateFactory;
use Gear\Traits\TFactory;

/**
 * Бибилиотека для компонентов, работающих с данными в базе данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GDbStorageComponent extends GComponent implements \IteratorAggregate, IFactory
{
    /* Traits */
    use TFactory;
    use TDelegateFactory;
    use TDbStorage;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_factoryProperties = [
        'class' => '\Gear\Library\GModel',
    ];
    protected $_alias = '';
    protected $_connection = null;
    protected $_connectionName = 'db';
    protected $_dbName = '';
    protected $_collectionName = '';
    protected $_defaultParams = [];
    /* Public */
}
