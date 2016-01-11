<?php

namespace gear\plugins\gear\loggers;

use \gear\Core;
use \gear\library\GPlugin;

/**
 * Запись данных протоколирования в файлы
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 15.06.2015
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GFileLogger extends GPlugin
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_properties = array
    (
        /* Путь к папке с файлами протоколов */
        'location' => 'logs',
        /* Права доступа к папке с файлами протоколов */
        'modeLocation' => 0700,
        /* Шаблон имени файла протокола */
        'templateFilename' => '%Y-%m-%d.log',
        /* Права доступа к файлам */
        'modeLogFile' => 0600,
        /* Данные протоколов каких уровней сохранять */
        'levels' => [Core::DEBUG, Core::CRITICAL, Core::WARNING, Core::ERROR],
        /* Максимальный размер файлов протоколов. 0 - не проверять размер файлов */
        'maxLogFileSize' => 10485760,
        /* Что делать, когда превышен размер файла */
        'overheadFileSize' => 'rotate', // rotate|overwrite
        /* Количество файлов участвующих в ротации */
        'maxRotateFiles' => 10,
    );
    /* Public */

    /**
     * Установка максимального размера файла с данными протоколов
     *
     * @access public
     * @param integer|string $size
     * @return $this
     */
    public function setMaxLogFileSize($size)
    {
        if (!is_numeric($size)) {
            $sizes = ['B', 'KB', 'MB', 'GB'];
            $format = preg_replace('/\d/', '', $size);
            $index = array_search($format, $sizes, true);
            $size = (int)$size * pow(1024, (int)$index);
        }
        $this->props('maxLogFileSize', (int)$size);
        return $this;
    }

    /**
     * Запись жанных протокола в файл
     *
     * @access public
     * @param string $level
     * @param string $message
     * @param string $dateTime
     * @return $this
     */
    public function write($level, $message, $dateTime = null)
    {
        if (in_array($level, $this->levels, true)) {
            $fileLog = $this->_prepareFilename();
            $dirname = dirname($fileLog);
            if (!file_exists($dirname)) {
                if (!is_writable(dirname($dirname)))
                    throw $this->exceptionFile('Could not create logs directory :dirname', ['dirname' => $dirname]);
                @mkdir($dirname);
                @chmod($dirname, $this->modeLocation);
            }
            if (!is_writable($dirname) || (file_exists($fileLog) && !is_writable($fileLog)))
                throw $this->exceptionFile('Log file :fileLog is not writable', ['fileLog' => $fileLog]);
            $handle = @fopen($fileLog, 'a');
            if ($handle) {
                @flock($handle, LOCK_EX);
                if (!$dateTime)
                    $dateTime = date('d/m/Y H:i:s');
                @fwrite($handle, "$dateTime [$level] $message\n");
                @flock($handle, LOCK_UN);
                fclose($handle);
            }
        }
        return $this;
    }

    /**
     * Получение пути к файлу
     *
     * @access public
     * @return string
     */
    protected function _prepareFilename()
    {
        $filename = $this->templateFilename;
        preg_match_all('#(\%[a-zA-Z]{1})#u', $this->templateFilename, $matches);
        if ($matches[0]) {
            foreach ($matches[0] as $item) {
                $item = substr($item, 1, 1);
                if ($item == 'c') {
                    $class = get_class($this->_owner);
                    $filename = str_replace('%' . $item, substr($class, strrpos($class, '\\') + 1), $filename);
                } else
                    $filename = str_replace('%' . $item, date($item), $filename);
            }
        }
        $filename = Core::resolvePath($this->location . '/' . $filename);
        if (file_exists($filename) && $this->maxLogFileSize > 0 && $this->maxLogFileSize <= filesize($filename)) {
            if ($this->overheadFileSize === 'rotate') {
                $this->_rotate($filename);
            } else
                @unlink($filename);
        }
        return $filename;
    }

    /**
     * Ротация файлов с данными протоколов
     *
     * @access public
     * @param string $filename
     * @return boolean
     */
    protected function _rotate($filename)
    {
        $max = $this->maxRotateFiles - 1;
        for ($i = $max; $i > 0; --$i) {
            if (is_file($filename . '.' . $i))
                @rename($filename . '.' . $i, $filename . '.' . ($i + 1));
        }
        @rename($filename, $filename . '.1');
        return true;
    }
}
