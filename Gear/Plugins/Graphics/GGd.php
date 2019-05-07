<?php

namespace Gear\Plugins\Graphics;

use Gear\Interfaces\FileInterface;
use Gear\Library\GPlugin;


/**
 * Плагин для работы с графикой посреством GD
 *
 * @package Gear Framework
 *
 * @property int height
 * @property FileInterface image
 * @property FileInterface owner
 * @property string path
 * @property null|resource resource
 * @property array size
 * @property int width
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class GGd extends GPlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_resource = null;
    /* Public */

    private function _prepareHeight($height)
    {
        return $this->_prepareValue($height, $this->height);
    }

    private function _prepareSize($width, $height)
    {
        return [$this->_prepareWidth($width), $this->_prepareHeight($height)];
    }

    private function _prepareValue($value, $original)
    {
        return preg_match('/%$/', $value) ? ($original * (int)$value) / 100 : $value;
    }

    private function _prepareWidth($width)
    {
        return $this->_prepareValue($width, $this->width);
    }

    /**
     * Возвращает высоту изображения
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHeight(): int
    {
        return getimagesize($this->path)[1];
    }

    /**
     * Возвращает исходное изображение
     *
     * @return FileInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getImage(): FileInterface
    {
        return $this->owner;
    }

    /**
     * Возвращает путь к изображению
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPath(): string
    {
        return $this->image->path;
    }

    /**
     * Возвращает ресурс изображения
     *
     * @return resource|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Возвращает размеры исходного изображения
     *
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getSize(): array
    {
        $size = getimagesize($this->path);
        return $size ? [$size[0], $size[1]] : [0, 0];
    }

    /**
     * Возвращает высоту изображения
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getWidth(): int
    {
        return getimagesize($this->path)[0];
    }

    /**
     * @param $width
     * @param $height
     * @param int $quality
     * @param string $mode
     * @param null $fileDestination
     * @return FileInterface|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function resize($width, $height, int $quality = 100, string $mode = 'proportional', $fileDestination = null): ?FileInterface
    {
        list($width, $height) = $this->_prepareSize($width, $height);
        $method = 'resize' . ucfirst($mode);
        try {
            $fileDestination = $this->$method($width, $height, $quality, $fileDestination);
        } catch (\Exception $e) {
            Core::c('logger')->exception($e->getMessage());
        } finally {
            return $fileDestination;
        }
    }

    public function resizeCover($width, $height, int $quality = 100, $fileDestination = null): ?FileInterface
    {
        if (!($sourceHandler = @imagecreatefromjpeg($this->path))) {
            return null;
        }
        list($imageWidth, $imageHeight) = $this->size;
        $srcRatio = $imageWidth / $imageHeight;
        $destRatio = $width / $height;
        if ($destRatio > $srcRatio) {
            $resizeWidth = $height * $srcRatio;
            $resizeHeight = $height;
            $y = 0;
            $x = (int)(150 - ($resizeWidth / 2));
        } else {
            $resizeHeight = $width / $srcRatio;
            $resizeWidth = $width;
            $x = 0;
            $y = (int)(($height - $resizeHeight) / 2);
        }
        $destinationHandler = imagecreatetruecolor($width, $height);
        imagecopyresampled($destinationHandler, $sourceHandler, $x, $y, 0, 0, $resizeWidth, $resizeHeight, $imageWidth, $imageHeight);
        if (!$fileDestination) {
            $fileDestination = $this->image;
        }
        imagejpeg($destinationHandler, $fileDestination, $quality);
        unset($sourceHandler, $destinationHandler);
        return $fileDestination;
    }

    public function resizeProportional($width, $height, int $quality = 100, $fileDestination = null)
    {
        if (!($sourceHandler = @imagecreatefromjpeg($this->path))) {
            return null;
        }
        list($imageWidth, $imageHeight) = $this->size;
        $srcRatio = $imageWidth / $imageHeight;
        $destRatio = $width / $height;
        if ($destRatio > $srcRatio) {
            $width = $height * $srcRatio;
        } else {
            $height = $width / $srcRatio;
        }
        $destinationHandler = imagecreatetruecolor($width, $height);
        imagecopyresampled($destinationHandler, $sourceHandler, 0, 0, 0, 0, $width, $height, $imageWidth, $imageHeight);
        if (!$fileDestination) {
            $fileDestination = $this->image;
        }
        imagejpeg($destinationHandler, $fileDestination, $quality);
        unset($sourceHandler, $destinationHandler);
        return $fileDestination;
    }

    /**
     * Устанавливает ресурс изображения
     *
     * @param resource $resource
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public function setResource($resource)
    {
        $this->_resource = $resource;
    }
}
