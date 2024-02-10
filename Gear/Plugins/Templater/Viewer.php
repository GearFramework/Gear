<?php

namespace Gear\Plugins\Templater;

use Gear\Core;
use Gear\Exceptions\Io\FileSystem\FileNotFoundException;
use Gear\Interfaces\Io\Filesystem\FileInterface;
use Gear\Interfaces\Templater\TemplateInterface;
use Gear\Interfaces\Templater\ViewableInterface;
use Gear\Interfaces\Templater\ViewerInterface;
use Gear\Interfaces\Templater\ViewOptionsInterface;
use Gear\Library\Services\Plugin;
use Stringable;

/**
 * Плагин - шаблонизатор
 *
 * @property array $renderSchemas
 * @property array $templateAliases
 * @property array $viewLayout
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class Viewer extends Plugin implements ViewerInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected array $renderSchemas = [];
    protected array $templateAliases = [];
    protected string $viewLayout = 'Views/Common/Layout';
    /* Public */

    /**
     * Возвращает путь, по которому лежит основной макет
     *
     * @return string|FileInterface
     */
    public function getViewLayout(): string|FileInterface
    {
        return $this->viewLayout;
    }

    /**
     * Установка пути, по которому лежит основной макет
     *
     * @param   string|FileInterface $viewLayout
     * @return  void
     */
    public function setViewLayout(string|FileInterface $viewLayout): void
    {
        $this->viewLayout = $viewLayout;
    }

    /**
     * @return array
     */
    public function getTemplateAliases(): array
    {
        return $this->templateAliases;
    }

    /**
     * @param   array $aliases
     * @return  void
     */
    public function setTemplateAliases(array $aliases): void
    {
        $this->templateAliases = $aliases;
    }

    /**
     * Возвращает шаблон по соответствующему ему алиасы, если такого шаблона
     * нет, то возвращает null
     *
     * @param   string $alias
     * @return  string|null
     */
    public function getTemplateByAlias(string $alias): ?string
    {
        return $this->templateAliases[$alias] ?? null;
    }

    /**
     * Обрабатывает и возвращает путь к файлу-шаблону
     *
     * @param   string|FileInterface $template
     * @return  string
     */
    private function getTemplateFile(string|FileInterface $template): string
    {
        /** @var ViewableInterface $owner */
        $owner = $this->getOwner();
        if (preg_match('#(\\\\|\/)#', $template) === false) {
            $template = "{$owner->viewPath}/{$template}";
        }
        $templateFilename = Core::resolvePath($template);
        if (!str_ends_with($templateFilename, '.phtml')) {
            $templateFilename .= '.phtml';
        }
        return $templateFilename;
    }

    /**
     * Возвращает карту расположения шаблонов
     *
     * @return array
     */
    public function getRenderSchemas(): array
    {
        return $this->renderSchemas;
    }

    /**
     * Установка карты расположения шаблонов
     *
     * @param   array $renderSchemas
     * @return  void
     */
    public function setRenderSchemas(array $renderSchemas): void
    {
        $this->renderSchemas = $renderSchemas;
    }

    /**
     * Возвращает схему отображения по её названию (ключу в массиве renderSchemas)
     * Если схема не найдена, то возвращается null
     *
     * @param   string $renderSchemaName
     * @return  array|null
     */
    public function getRenderSchemaByName(string $renderSchemaName): ?array
    {
        return $this->renderSchemas[$renderSchemaName] ?? null;
    }

    public function getDefaultOptions(): ViewOptionsInterface
    {
        return new ViewOptions([
            'buffered'  => false,
            'useLayout' => false,
        ]);
    }

    /**
     * Отображение шаблона
     *
     * @param   string|FileInterface|TemplateInterface  $template
     * @param   array                                   $context
     * @param   null|ViewOptionsInterface               $options
     * @return  bool|string
     */
    public function render(
        string|FileInterface|TemplateInterface $template,
        array $context = [],
        ?ViewOptionsInterface $options = null,
    ): bool|string {
        if ($options === null) {
            $options = $this->getDefaultOptions();
        }
        if ($template instanceof TemplateInterface) {
            return $template->render($context, $options);
        }
        /** @var string|Stringable $template */
        if ($alias = $this->getTemplateByAlias($template)) {
            $template = $alias;
        }
        if ($schema = $this->getRenderSchemaByName($template)) {
            return $this->renderSchema($schema, $context, $options);
        }
        if ($options->useLayout && $this->viewLayout) {
            $context['layoutContent'] = $this->render($this->viewLayout, $context, new ViewOptions([
                'buffered'  => true,
                'useLayout' => false,
            ]));
        }
        $template = $this->getTemplateFile($template);
        return $this->renderFile($template, $context, $options);
    }

    /**
     * Подключение .phtml файла с шаблоном отображения
     *
     * @param   string|FileInterface        $filePath
     * @param   array                       $context
     * @param   null|ViewOptionsInterface   $options
     * @return  bool|string
     */
    public function renderFile(
        string|FileInterface $filePath,
        array $context = [],
        ?ViewOptionsInterface $options = null,
    ): bool|string {
        return $this->renderProcess($filePath, $context, $options ?: $this->getDefaultOptions());
    }

    /**
     * Обработка схемы подключения .phtml файлов и их отображения
     *
     * @param   array                     $schema
     * @param   array                     $context
     * @param   null|ViewOptionsInterface $options
     * @return  bool|string
     */
    public function renderSchema(
        array $schema,
        array $context = [],
        ?ViewOptionsInterface $options = null
    ): bool|string {
        $blockOptions = new ViewOptions([
            'buffered'  => true,
            'useLayout' => false
        ]);
        foreach ($schema as $contentBlockName => $template) {
            $context[$contentBlockName] = $this->render($template, $context, $blockOptions);
        }
        return $this->render($this->getViewLayout(), $context, $options ?: $this->getDefaultOptions());
    }

    /**
     * Подключение .phtml файла с шаблоном отображения
     *
     * @param   string                    $__render__filePath__
     * @param   array                     $__render__context__
     * @param   null|ViewOptionsInterface $__render__options__
     * @return  bool|string
     */
    private function renderProcess(
        string $__render__filePath__,
        array $__render__context__ = [],
        ?ViewOptionsInterface $__render__options__ = null,
    ): bool|string {
        if (file_exists($__render__filePath__) === false || is_readable($__render__filePath__) === false) {
            throw Core::{FileNotFoundException::class}(['filename' => $__render__filePath__]);
        }
        if ($__render__context__) {
            extract($__render__context__);
        }
        if ($__render__options__->buffered) {
            $__old__data__ = null;
            $__buffered__ = false;
            if (ob_get_status()) {
                $__buffered__ = true;
                $__old__data__ = ob_get_contents();
                ob_clean();
            } else {
                ob_start();
            }
            require $__render__filePath__;
            $__result__view__rendered__ = ob_get_contents();
            ob_clean();
            if ($__buffered__ && $__old__data__) {
                echo $__old__data__;
            }
            if (empty($__buffered__)) {
                ob_end_flush();
            }
            return $__result__view__rendered__;
        }
        require $__render__filePath__;
        return true;
    }
}
