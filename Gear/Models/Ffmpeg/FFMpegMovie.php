<?php

namespace Gear\Models\Ffmpeg;

use Gear\Interfaces\DependentInterface;
use Gear\Library\GModel;
use Gear\Library\Io\Filesystem\GFile;

/**
 * Модель видео-файла
 *
 * @package Gear Framework
 *
 * @property array info
 * @property string ffmpegCommand
 * @property string ffprobeCommand
 * @property string tempDir
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class FFMpegMovie extends GFile implements DependentInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_info = null;
    /* Public */

    public function _parseFormat($section)
    {
        foreach ($section as $name => $value) {
            $this->_info[$name] = $value;
        }
    }

    public function _parseStreams($section)
    {
        $video = [];
        $audio = [];
        if (count($section) > 1) {
            list($video, $audio) = $section;
        } else {
            $video = reset($section);
        }
        $this->_info['video'] = new GModel($video);
        $this->_info['audio'] = new GModel($audio);
    }

    public function getCountFrames(): int
    {
        return (int)$this->info->video->nb_frames;
    }

    public function getDuration(): int
    {
        return (int)$this->info['duration'];
    }

    public function getFrames(int $count, $start = 0, $step = 1): array
    {

        $command = $this->ffmpegCommand . ' -i ' . $this . " -r 1 -t 00:00:01 -f image2 " . $this->tempDir . '/image%02d.jpg';
    }

    public function getHeight(): int
    {
        return (int)$this->info->video->height;
    }

    public function getInfo()
    {
        if (!$this->_info) {
            $out = null;
            $ret = null;
            exec($this->ffprobeCommand . ' -v quiet -print_format json -show_format -show_streams "' . $this->path . '" 2>&1'. $out, $ret);
            if (is_array($ret)) {
                $ret = join("", $ret);
                $ret = json_decode($ret, true);
                foreach ($ret as $sectionName => $section) {
                    $method = '_parse' . $sectionName;
                    if (method_exists($this, $method)) {
                        $this->$method($section);
                    }
                }
            }
        }
        return $this->_info;
    }

    public function getWidth(): int
    {
        return (int)$this->info->video->width;
    }

    public function setInfo(array $info)
    {
        $this->_info = $info;
    }
}
