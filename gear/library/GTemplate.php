<?php

namespace gear\library;

use gear\traits\TView;

/**
 * Класс шаблонов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GTemplate extends GModel
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    public function build($context)
    {
        $result = [];
        foreach($this->binds as $bindName => $template) {
            if (!is_array($template)) {
                $result[$bindName]['content'] = $this->view->render($template, $context, true);
            } else {
                if (isset($template['options'])) {
                    $result[$bindName]['options'] = $template['options'];
                }
                $result[$bindName]['content'] = $this->view->render($template['view'], $context, true);
            }
        }
        return $result;
    }
}