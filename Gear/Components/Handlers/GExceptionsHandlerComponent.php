<?php

namespace Gear\Components\Handlers;

use Gear\Core;
use Gear\Library\GComponent;

/**
 * Класс обработчик неперехваченных исключений
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GExceptionsHandlerComponent extends GComponent
{
    /* Trait */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected static $_config = ['handler' => 'exception'];
    protected $_viewPath = [
        'mode' => [
            Core::AJAX => '\gear\views\exceptionAjax',
            Core::HTTP => '\gear\views\exceptionHttp',
            Core::HTTPS => '\gear\views\exceptionHttp',
            Core::CLI => '\gear\views\exceptionConsole',
        ],
    ];
    /* Public */

    /**
     * Обработчик исключений, которые не были перехвачены try {} catch {}
     *
     * @param \Throwable $e
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exception(\Throwable $e)
    {
        try {
            if (php_sapi_name() !== 'cli' && !empty(ob_get_status())) {
                ob_end_clean();
            }
            $this->view->render($this->getViewPath(), ['exception' => $e]);
            die();
        } catch (\Throwable $e) {
            die($e->getMessage() . "\n" . $e->getFile() . "[" . $e->getLine() . "]\n" . $e->getTraceAsString() . "\n");
        }
    }

    /**
     * Возвращает путь к шаблону отображения
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getViewPath(): string
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
        return $this->_viewPath['mode'][$mode];
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
     * Генерация события onAfterInstallService после процедуры установки сервиса
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterInstallService()
    {
        if (!($handlerName = static::i('handler'))) {
            throw self::exceptionService('Not specified <{handler}> property', ['handler' => 'handler']);
        }
        set_exception_handler([$this, $handlerName]);
        return parent::afterInstallService();
    }
}
