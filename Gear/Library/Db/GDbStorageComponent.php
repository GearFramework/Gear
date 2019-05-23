<?php

namespace Gear\Library\Db;

use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbStorageComponentInterface;
use Gear\Interfaces\FactoryInterface;
use Gear\Library\GComponent;
use Gear\Traits\Factory\DelegateFactoryTrait;
use Gear\Traits\Factory\FactoryTrait;

/**
 * Бибилиотека для компонентов, работающих с данными в базе данных
 *
 * @package Gear Framework
 *
 * @property string alias
 * @property string collectionName
 * @property DbConnectionInterface $connection
 * @property string connectionName
 * @property string dbName
 * @property array defaultParams
 * @property string primaryKey
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
abstract class GDbStorageComponent extends GComponent implements \IteratorAggregate, FactoryInterface, DbStorageComponentInterface
{
    /* Traits */
    use DelegateFactoryTrait;
    use FactoryTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_factoryProperties = [
        'class' => '\Gear\Library\GModel',
    ];
    protected $_alias = '';
    protected $_connection = null;
    protected $_connectionName = 'db';
    protected $_dbName = '';
    protected $_collectionName = '';
    protected $_defaultParams = [];
    protected $_primaryKey = 'id';
    /* Public */
}
