<?php

namespace Gear\Models\Ffmpeg;

use Gear\Interfaces\DependentInterface;
use Gear\Library\GCollection;
use Gear\Library\GModel;
use Gear\Library\Io\Filesystem\GFile;
use Gear\Library\Io\Filesystem\GFileSystem;
use Gear\Models\Calendar\GTimeInterval;

/**
 * Модель видео-файла
 *
 * @package Gear Framework
 *
 * @property GTimeInterval duration
 * @property int height
 * @property array info
 * @property string ffmpegCommand
 * @property string ffprobeCommand
 * @property string tempDir
 * @property int width
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

    /**
     * Возвращает количество фреймов
     *
     * @return int
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getCountFrames(): int
    {
        return (int)$this->info->video->nb_frames;
    }

    /**
     * Возвращает продолжительность ролика
     *
     * @return GTimeInterval
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getDuration(): GTimeInterval
    {
        return \Calendar::interval((int)$this->info['duration']);
    }

    public function getFrame($start = 0): ?GFile
    {
        $start = new GTimeInterval(['interval' => $start]);
        $fileFrame = $this->tempDir . '/' . $this->name . '_frame.jpg';
        $command = $this->ffmpegCommand . ' -i ' . $this . ' -r 1 -t 00:00:01 -ss ' . $start . ' -f image2 "' . $fileFrame . '" 2>&1';
        $out = null;
        $ret = null;
        exec($command, $out, $ret);
        /** @var GFile $frame */
        $frame = null;
        if (file_exists($fileFrame)) {
            $frame = GFileSystem::factory(['path' => $fileFrame]);
        }
        return $frame;
    }

    /**
     * @param int $count
     * @param int $start
     * @param int $step
     * @return iterable
     */
    public function getFrames(int $count, $start = 0, $step = 0): iterable
    {
        $start = new GTimeInterval(['interval' => $start]);
        if (!$step) {
            $step = (int)(($this->duration->interval - $start->interval) / $count);
        }
        $positions = [];
        if ($count > 1) {
            $countFrames = $this->duration->interval;
            $pos = clone $start;
            for ($i = 0; $i < $count; ++ $i) {
                if ($pos->interval > $countFrames) {
                    break;
                }
                $positions[] = clone $pos;
                $pos->addInterval($step);
            }
        } else {
            $positions = [$start];
        }
        $frames = [];
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 777, true);
        }
        foreach ($positions as $i => $pos) {
            $fileFrame = $this->tempDir . '/' . $this->name . '_frame' . ($i + 1) . '.jpg';
            $command = $this->ffmpegCommand . ' -i ' . $this . ' -r 1 -t 00:00:01 -ss ' . $pos . ' -f image2 "' . $fileFrame . '" 2>&1';
            $out = null;
            $ret = null;
            exec($command, $out, $ret);
            if (file_exists($fileFrame)) {
                $frames[] = GFileSystem::factory(['path' => $fileFrame]);
            }
        }
        return new GCollection($frames);
    }

    public function getRandomFrame(): ?GFile
    {
        try {
            $start = random_int(0, $this->duration->interval);
        } catch (\Exception $e) {
            $start = mt_rand(0, $this->duration);
        }
        $start = new GTimeInterval(['interval' => $start]);
        $fileFrame = $this->tempDir . '/' . $this->name . '_frame.jpg';
        $command = $this->ffmpegCommand . ' -i ' . $this . ' -r 1 -t 00:00:01 -ss ' . $start . ' -f image2 "' . $fileFrame . '" 2>&1';
        $out = null;
        $ret = null;
        exec($command, $out, $ret);
        /** @var GFile $frame */
        $frame = null;
        if (file_exists($fileFrame)) {
            $frame = GFileSystem::factory(['path' => $fileFrame]);
        }
        return $frame;
    }

    /**
     * Возвращает высоту
     *
     * @return int
     * @since 0.0.2
     * @version 0.0.2
     */
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

    /**
     * Возвращает ширину
     *
     * @return int
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getWidth(): int
    {
        return (int)$this->info->video->width;
    }

    public function setInfo(array $info)
    {
        $this->_info = $info;
    }
}
