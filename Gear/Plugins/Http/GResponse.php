<?php

namespace Gear\Plugins\Http;

use Gear\Core;
use Gear\Interfaces\DirectoryInterface;
use Gear\Interfaces\FileInterface;
use Gear\Interfaces\ResponseInterface;
use Gear\Library\GPlugin;
use Gear\Library\Io\Filesystem\GDirectory;
use Gear\Library\Io\Filesystem\GFile;
use Gear\Traits\Http\MessageTrait;
use Gear\Traits\Http\ResponseTrait;

/**
 * Плагин для работы с ответами на запросы пользователей
 *
 * @package Gear Framework
 *
 * @property array headers
 * @property string protocolVersion
 * @property string reasonPhrase
 * @property int statusCode
 * @property string|DirectoryInterface tempDirectory
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GResponse extends GPlugin implements ResponseInterface
{
    /* Traits */
    use MessageTrait;
    use ResponseTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_headers = [
        'Content-Type' => 'text/html',
    ];
    protected $_tempDirectory = '/tmp';
    /* Public */

    public function getTempDirectory(): DirectoryInterface
    {
        if (!($this->_tempDirectory) instanceof DirectoryInterface) {
            $this->_tempDirectory = new GDirectory(['path' => $this->_tempDirectory]);
        }
        return $this->_tempDirectory;
    }

    /**
     * Отправляет клиенту данные
     *
     * @param mixed $data
     * @param array $headers
     * @return mixed
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function send($data, array $headers = [])
    {
        if (is_numeric($data) && isset(self::$_phrases[(int)$data])) {
            header("HTTP/1.0 $data");
            return;
        }
        if (is_string($data) && preg_match('#^HTTP/\d\.\d (\d+)#', $data, $math)) {
            if (Core::app()->request->isAjax()) {
                $data = ['error' => $math[1]];
            } else {
                header($data, true, $math[1]);
                return;
            }
        }
        if (Core::app()->request->isAjax()) {
            if (is_array($data)) {
                $data = json_encode($data);
            } elseif (!is_string($data)) {
                $this->sendStatus(200);
                return;
            }
        } else {
            if ($data instanceof DirectoryInterface) {
                return $this->sendDirectory($data, $headers);
            } elseif ($data instanceof FileInterface) {
                return $this->sendFile($data, $headers);
            } elseif (is_array($data)) {
                $data = json_encode($data);
            } else if (!is_string($data)) {
                $this->sendStatus(200);
                return;
            }
        }
        $this->sendStatus(200);
        $this->sendHeaders();
        echo $data;
    }

    public function sendDirectory(DirectoryInterface $dir, array $headers = [])
    {
        //TODO:Упаковка директории в архив и отправка со всеми заголовками
        if ($dir->exists()) {
            //TODO:Архивирование директории
            $headers = array_merge([
                'HTTP/1.1 200 OK',
                'Content-Type' => 'application/zip',
                'Content-Length' => 0
            ], $headers);
        }
    }

    /**
     * Отправка файла
     *
     * @param FileInterface $file
     * @param array $headers
     * @param int $speed
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public function sendFile(FileInterface $file, array $headers = [], $speed = 0)
    {
        if ($file->exists()) {
            $headers = array_merge([
                'Content-Type' => $file->mime,
                'Content-Length' => $file->size
            ], $headers);
            $this->setHeaders($headers);
            $this->sendHeaders();
            if (!$speed) {
                echo $file->content;
            } else {
                set_time_limit(0);
                if (ob_get_status()) {
                    ob_end_clean();
                }
                $file->open(['mode' => 'r']);
                foreach ($file->read($speed) as $data) {
                    echo $data;
                }
                $file->close();
            }
        } else {
            $this->sendStatus(404);
        }
    }

    /**
     * Отправка установленных заголовков
     *
     * @return ResponseInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function sendHeaders(): ResponseInterface
    {
        foreach ($this->headers as $header => $value) {
            if (is_numeric($header)) {
                header($value);
            } else {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                header("$header: $value");
            }
        }
        return $this;
    }

    /**
     * Отправка заголовка-ответа с указанным статусом
     *
     * @param $code
     * @return ResponseInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sendStatus($code): ResponseInterface
    {
        if (!isset(self::$_phrases[$code])) {
            $code = 306;
        }
        header("HTTP/" . $this->protocolVersion . " $code " . isset(self::$_phrases[$code]), true, $code);
        return $this;
    }

    public function setTempDirectory(string $dir)
    {
        $this->_tempDirectory = $dir;
    }
}
