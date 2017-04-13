<?php

namespace gear\components\log\plugins\images;

use gear\interfaces\IFile;
use gear\library\GPlugin;

/**
 * Плагин для работы с графикой с помощью GD
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GGd extends GPlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_handler = null;
    protected $_names = [
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/bmp' => 'bmp',
    ];
    /* Public */

    /**
     * Подготовка объекта-параметров из массива
     *
     * @param $options
     * @return GFileSystemOptions
     * @since 0.0.1
     * @version 0.0.1
     */
    protected function _prepareOptions($options): GFileSystemOptions
    {
        if (is_array($options)) {
            $options = new GFileSystemOptions($options);
        }
        return $options;
    }

    public function getHandler()
    {
        return $this->_handler;
    }

    /**
     * Открывает изображение для работы с ним
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function open()
    {
        if (!isset($this->_names[$this->owner->mime])) {
            throw self::GdException('Invalid format');
        }
        $method = 'imagecreatefrom' . $this->_names[$this->owner->mime];
        $this->handler = $method($this->owner);
    }

    public function resize(int $width, int $height, $options = []): IFile
    {

    }

    public function save($options = [])
    {

    }

    public function setHandler($handler)
    {
        $this->_handler = $handler;
    }
}
