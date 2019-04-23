<?php

namespace Gear\Components\Ffmpeg;

use Gear\Library\GComponent;
use Gear\Models\Ffmpeg\FFMpegMovie;
use Gear\Traits\Factory\FactoryTrait;

/**
 * Тестовый компонент для работы с ffmpeg
 *
 * @package Gear Framework
 *
 * @property string shellCommand
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class FFMpegGear extends GComponent
{
    /* Traits */
    use FactoryTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_factoryProperties = [
        'class' => '\Gear\Models\Ffmpeg\FFMpegMovie',
    ];
    protected $_shellCommand = '/usr/bin/ffmpeg';
    /* Public */

    public function getShellCommand(): string
    {
        return $this->_shellCommand;
    }

    public function open($pathToFile): FFMpegMovie
    {
        return new FFMpegMovie(['path' => $pathToFile]);
    }

    public function setShellCommand(string $command)
    {
        $this->_shellCommand = $command;
    }
}
