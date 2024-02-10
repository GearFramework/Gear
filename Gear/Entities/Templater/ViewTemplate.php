<?php

namespace Gear\Entities\Templater;

use Gear\Interfaces\Templater\TemplateInterface;
use Gear\Interfaces\Templater\ViewOptionsInterface;
use Gear\Library\Objects\Entity;
use Gear\Plugins\Templater\ViewOptions;

/**
 * Класс шаблона отображения
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
class ViewTemplate extends Entity implements TemplateInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected array $schema = [
        'template'  => '',
        'blocks'    => [
            'head'   => '',
            'body'   => '',
            'footer' => '',
        ],
    ];
    /* Public */

    /**
     * Функция рендеринга шаблона
     *
     * @param   array                     $context
     * @param   ViewOptionsInterface|null $options
     * @return  bool|string
     */
    public function render(array $context = [], ?ViewOptionsInterface $options = null): bool|string
    {
        $result = $this->renderBlocks(
            $this->schema['blocks'] ?? [],
            $context,
            new ViewOptions([
                'buffered'  => true,
                'useLayout' => false,
            ]),
        );
        $context = array_merge($context, $result);
        return $this->getViewer()->render($this->schema['template'] ?? '', $context, $options);
    }

    /**
     * Рендеринг отдельных блоков, входящих в шаблон
     *
     * @param   array                     $blocks
     * @param   array                     $context
     * @param   ViewOptionsInterface|null $options
     * @return  array
     */
    protected function renderBlocks(array $blocks, array $context, ?ViewOptionsInterface $options): array
    {
        foreach ($blocks as $blockName => $template) {

        }
    }
}
