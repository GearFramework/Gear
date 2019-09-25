<?php

namespace Gear\Components\Ffmpeg;

use Gear\Interfaces\FileInterface;
use Gear\Library\GComponent;
use Gear\Library\Io\Filesystem\GFileSystem;
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
    protected $_ffmpegCommand = '/usr/bin/ffmpeg';
    protected $_ffprobeCommand = '/usr/bin/ffprobe';
    protected $_model = [
        'class' => '\Gear\Models\Ffmpeg\FFMpegMovie',
    ];
    protected $_tempDir = '/tmp';
    /* Public */

    public function getFfmpegCommand(): string
    {
        return $this->_ffmpegCommand;
    }

    public function getFfprobeCommand(): string
    {
        return $this->_ffprobeCommand;
    }

    public function getTempDir(): string
    {
        return $this->_tempDir;
    }

    public function open($pathToFile): FFMpegMovie
    {
        return $this->factory(['path' => $pathToFile], $this);
    }

    public function setFfmpegCommand(string $command)
    {
        $this->_ffmpegCommand = $command;
    }

    public function setFfprobeCommand(string $command)
    {
        $this->_ffprobeCommand = $command;
    }

    public function setTempDir(string $dir)
    {
        $this->_tempDir = $dir;
    }

    public function createGifFromFiles($dir, string $filesTemplate, $filenameGif): ?FileInterface
    {
        $out = null;
        $ret = null;
        /** @var FileInterface $file */
        $file = null;
        exec($this->ffmpegCommand . ' -f image2 -framerate 3 -i ' . $dir . '/' . $filesTemplate . ' ' . $filenameGif . ' 2>&1', $out, $ret);
        if (file_exists($filenameGif)) {
            $file = GFileSystem::factory(['path' => $filenameGif]);
        }
        return $file;
    }
}
