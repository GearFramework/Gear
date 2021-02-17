<?php

namespace Gear\Components\Handlers;

use Gear\Core;
use Gear\Library\GComponent;

/**
 * Класс обработчик неперехваченных исключений
 *
 * @package Gear Framework
 *
 * @property int mode
 * @property string viewPath
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GExceptionsHandlerComponent extends GComponent
{
    /* Trait */
    /* Const */
    /* Private */
    /* Protected */
    protected static array $_config = [
        'handler' => 'exception',
    ];
    protected $_viewPath = [
        'mode' => [
            Core::AJAX => '\Gear\Views\ExceptionAjax',
            Core::HTTP => '\Gear\Views\ExceptionHttp',
            Core::HTTPS => '\Gear\Views\ExceptionHttp',
            Core::CLI => '\Gear\Views\ExceptionConsole',
        ],
    ];
    /* Public */

    /**
     * Обработчик исключений, которые не были перехвачены try/catch
     *
     * @param \Throwable $e
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function exception(\Throwable $e)
    {
        try {
            if ($this->mode !== Core::CLI && !empty(ob_get_status())) {
                ob_end_clean();
            }
            $this->view->render($this->getViewPath(), ['exception' => $e]);
        } catch (\Throwable $e) {
            if (defined('DEBUG') && DEBUG) {
                $message = $e->getMessage() . "\n" . $e->getFile() . "[" . $e->getLine() . "]\n" . $e->getTraceAsString() . "\n";
                echo $this->mode !== Core::CLI ? nl2br($message) : $message;
            } else {
                header('HTTP/1.1 502');
            }
        } finally {
            die();
        }
    }

    public function getMode(): int
    {
        if (php_sapi_name() === 'cli') {
            $mode = Core::CLI;
        } elseif (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $mode = Core::AJAX;
        } elseif (isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) {
            $mode = Core::HTTPS;
        } else {
            $mode = Core::HTTP;
        }
    }

    /**
     * Получение куска исходного кода указанного php-файла относительно
     * указанного номера строки
     *
     * @param string $file
     * @param integer $currentLine
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getSource(string $file, int $currentLine = 0): array
    {
        $sources = [];
        if (is_file($file) && file_exists($file) && is_readable($file)) {
            $lines = file($file);
            $count = count($lines);
            if (!$currentLine) {
                $startLine = 0;
                $endLine = $count <= 10 ? $count - 1 : 9;
            } else {
                $startLine = $currentLine - 10 >= 0 ? $currentLine - 10 : 0;
                $endLine = $currentLine + 10 < $count ? $currentLine + 9 : $count - 1;
            }
            for ($i = $startLine; $i <= $endLine; ++$i)
                $sources[$i + 1] = $lines[$i];
        }
        return $sources;
    }

    /**
     * Возвращает путь к шаблону отображения
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getViewPath(): string
    {
        return $this->_viewPath['mode'][$this->mode];
    }

    /**
     * Подсветка синтаксиса
     *
     * @param string $source
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function highlight(string $source): string
    {
        $source = highlight_string($source, true);
        return $source;
    }

    /**
     * Обработчик события onAfterInstallService после процедуры установки сервиса
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.2
     */
    public function onAfterInstallService()
    {
        if (!($handlerName = static::i('handler'))) {
            throw self::ServiceException('Not specified <{handler}> property', ['handler' => 'handler']);
        }
        set_exception_handler([$this, $handlerName]);
        return true;
    }
}
