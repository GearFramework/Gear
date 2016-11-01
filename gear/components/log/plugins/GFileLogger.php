<?php

namespace gear\components\log\plugins;

use gear\Core;
use gear\library\GPlugin;

/**
 * Плагин записи логов в файл
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @license MIT
 * @since 31.08.2016
 * @version 1.0.0
 */
class GFileLogger extends GPlugin
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_defaultProperties = [
        'location' => 'log/application.log',
        'modeLocation' => 0700,
        'modeLogFile' => 0600,
        'levels' => [Core::ERROR, Core::WARNING, Core::NOTICE, Core::INFO],
        'maxLogFileSize' => 10 * 1048576,
        /* Что делать, когда превышен размер файла rotate|overwrite */
        'overheadFileSize' => 'overwrite',
        /* Количество файлов участвующих в ротации */
        'maxRotateFiles' => 10,
    ];
    protected static $_isInit = false;
    /* Public */

    /**
     * Запись данных протокола в файл
     *
     * @access public
     * @param string $level
     * @param string $message
     * @param array $context
     * @throws FileSystemException
     * @since 1.0.0
     */
    public function log($level, $message, array $context = [])
    {
        if (in_array($level, $this->levels, true)) {
            $fileLog = $this->location;
            $dirname = dirname($fileLog);
            if (!file_exists($dirname)) {
                if (!is_writable(dirname($dirname)))
                    throw $this->exceptionFileSystem('Could not create logs directory {dirname}', ['dirname' => $dirname]);
                @mkdir($dirname);
                @chmod($dirname, $this->modeLocation);
            }
            if (!is_writable($dirname) || (file_exists($fileLog) && !is_writable($fileLog)))
                throw $this->exceptionFileSystem('Log file {fileLog} is not writable', ['fileLog' => $fileLog]);
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
     * @access public
     * @param integer|string $size
     * @since 1.0.0
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
     * @access protected
     * @return string
     * @since 1.0.0
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
                } else
                    $filename = str_replace('%' . $item, date($item), $filename);
            }
        }
        if (file_exists($filename) && $this->maxLogFileSize > 0 && $this->maxLogFileSize <= filesize($filename)) {
            if ($this->overheadFileSize === 'rotate') {
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
     * @access protected
     * @param string $filename
     * @return boolean
     * @since 1.0.0
     */
    public function rotate($filename)
    {
        $max = $this->maxRotateFiles - 1;
        for ($i = $max; $i > 0; --$i) {
            if (is_file($filename . '.' . $i))
                @rename($filename . '.' . $i, $filename . '.' . ($i + 1));
        }
        @rename($filename, $filename . '.1');
        return true;
    }

    /**
     * Возвращает true, если логгер обрабатывает указанный уровень сообщений, иначе false
     *
     * @access public
     * @param string $level
     * @return bool
     * @since 1.0.0
     */
    public function hasLevel($level)
    {
        return in_array($level, $this->levels);
    }

    /**
     * Генерация события onBeforeInstallService перед установкой плагина
     *
     * @access public
     * @static
     * @param array $config
     * @param array $properties
     * @param null|object $owner
     * @return mixed
     * @since 1.0.0
     */
    public static function beforeInstallService($config, $properties, $owner)
    {
        return true;
    }

    /**
     * Генерация события onAfterInstallService после процедуры установки сервиса
     *
     * @access public
     * @return mixed
     * @since 1.0.0
     */
    public function afterInstallService()
    {
        return true;
    }

    /**
     * Генерация события onAfterUninstallService после процедуры деинсталляции сервиса
     *
     * @access public
     * @return mixed
     * @since 1.0.0
     */
    public function afterUninstallService()
    {
        return true;
    }
}