<?php

namespace Gear\Library\Io;

use Gear\Interfaces\IoInterface;
use Gear\Interfaces\StaticFactoryInterface;
use Gear\Library\GModel;
use Gear\Traits\Factory\StaticFactoryTrait;

/**
 * Класс ввода-вывода
 *
 * @package Gear Framework
 *
 * @property mixed handler
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
abstract class GIo extends GModel implements IoInterface, StaticFactoryInterface
{
    /* Traits */
    use StaticFactoryTrait;
    /* Const */
    const DEFAULT_LENGTH_READ = 8 * 1024;
    const UNKNOWN = 0;
    const FIFO = 1;
    const CHAR = 2;
    const DIR = 3;
    const BLOCK = 4;
    const LINK = 5;
    const FILE = 6;
    const SOCKET = 7;
    /* Private */
    /* Protected */
    protected $_handler = null;
    protected $_types = [
        self::UNKNOWN => 'unknown',
        self::FIFO => 'fifo',
        self::CHAR => 'char',
        self::DIR => 'dir',
        self::BLOCK => 'block',
        self::LINK => 'link',
        self::FILE => 'file',
        self::SOCKET => 'socket',
    ];
    /* Public */

    abstract public function close();

    /**
     * При $type равным
     * NULL - возращает тип элемента соответствующее одному из значений
     *        GFileSystem::FILE|GFileSystem::FOLDER|GFileSystem::LINK
     * целочисленное значение
     * целое число - возращает
     *        true или false при соответствии
     * строковое значение - возвращает true или false при
     *        соответствии
     *
     * @param mixed $type
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isA($type = null)
    {
        $result = false;
        if ($type === null)
            $result = array_search($this->type(), $this->_types);
        else if (is_numeric($type))
            $result = array_search($this->type(), $this->_types) === (int)$type;
        else if (is_string($type))
            $result = $this->type() === $type;
        return $result;
    }

    abstract public function open($options = []);

    abstract public function read($length = self::DEFAULT_LENGTH_READ);

    abstract public function seek($offset);

    /**
     * Возвращает строковое значение соответствующее типу элемента
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function type();

    abstract public function write($data, $length = 0);
}
