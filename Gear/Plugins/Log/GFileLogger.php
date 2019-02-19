<?php

namespace Gear\Plugins\Log;

use Gear\Core;
use Gear\Library\GPlugin;

/**
 * Плагин записи логов в файл
 *
 * @package Gear Framework
 *
 * @property array levels
 * @property string location
 * @property int modeLocation
 * @property int modeLogGile
 * @property int maxLogFileSize
 * @property int overheadFileSize
 * @property int maxRotateFiles
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GFileLogger extends GPlugin
{
    /* Traits */
    /* Const */
    const ROTATE = 1;
    const OVERWRITE = 2;
    /* Private */
    /* Protected */
    protected static $_defaultProperties = [
        'levels' => [Core::ERROR, Core::WARNING, Core::NOTICE, Core::INFO],
        'location' => 'Log/application.log',
        'modeLocation' => 0700,
        'modeLogFile' => 0600,
        'maxLogFileSize' => 10 * 1048576,
        /* Что делать, когда превышен размер файла rotate|overwrite */
        'overheadFileSize' => self::OVERWRITE,
        /* Количество файлов участвующих в ротации */
        'maxRotateFiles' => 10,
    ];
    protected static $_isInit = false;
    /* Public */

    /**
     * Запись данных протокола в файл
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @since 0.0.1
     * @version 0.0.2
     */
    public function log(string $level, string $message, array $context = [])
    {
        if (in_array($level, $this->levels, true)) {
            $fileLog = $this->location;
            $dirname = dirname($fileLog);
            if (!file_exists($dirname)) {
                if (!is_writable(dirname($dirname))) {
                    throw $this->FileSystemException('Could not create logs directory {dirname}', ['dirname' => $dirname]);
                }
                @mkdir($dirname);
                @chmod($dirname, $this->modeLocation);
            }
            if (!is_writable($dirname) || (file_exists($fileLog) && !is_writable($fileLog))) {
                throw $this->FileSystemException('Log file {fileLog} is not writable', ['fileLog' => $fileLog]);
            }
            $handle = @fopen($fileLog, 'a');
            if ($handle) {
                @flock($handle, LOCK_EX);
                $log = date('d/m/Y H:i:s') . ' FILELOG [' . strtoupper($level) . '] ' . $message . "\n";
                @fwrite($handle, $log);
                @flock($handle, LOCK_UN);
                fclose($handle);
            }
        }
    }

    /**
     * Установка максимального размера файла с данными протоколов
     *
     * @param integer|string $size
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setMaxLogFileSize($size)
    {
        if (!is_numeric($size)) {
            $sizes = ['B', 'KB', 'MB', 'GB'];
            $format = preg_replace('/\d/', '', strtoupper($size));
            $index = array_search($format, $sizes, true);
            $size = (int)$size * pow(1024, (int)$index);
        }
        $this->props('maxLogFileSize', (int)$size);
    }

    /**
     * Получение пути к файлу
     *
     * @return string
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getLocation()
    {
        $filename = Core::resolvePath($this->props('location'), true);
        preg_match_all('#(\%[a-zA-Z]{1})#u', $filename, $matches);
        if ($matches[0]) {
            foreach ($matches[0] as $item) {
                $item = substr($item, 1, 1);
                if ($item == 'c') {
                    $class = get_class($this->owner);
                    $filename = str_replace('%' . $item, substr($class, strrpos($class, '\\') + 1), $filename);
                } else {
                    $filename = str_replace('%' . $item, date($item), $filename);
                }
            }
        }
        if (file_exists($filename) && $this->maxLogFileSize > 0 && $this->maxLogFileSize <= filesize($filename)) {
            if ($this->overheadFileSize === self::ROTATE) {
                $this->rotate($filename);
            } else {
                @unlink($filename);
            }
        }
        return $filename;
    }

    /**
     * Ротация файлов с данными протоколов
     *
     * @param string $filename
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function rotate(string $filename)
    {
        $max = $this->maxRotateFiles - 1;
        for ($i = $max; $i > 0; --$i) {
            if (is_file($filename . '.' . $i)) {
                @rename($filename . '.' . $i, $filename . '.' . ($i + 1));
            }
        }
        @rename($filename, $filename . '.1');
    }

    /**
     * Возвращает true, если логгер обрабатывает указанный уровень сообщений, иначе false
     *
     * @param string $level
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function hasLevel(string $level): bool
    {
        return in_array($level, $this->levels, true);
    }
}
