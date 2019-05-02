<?php

namespace Gear\Models\Html;

use Gear\Library\GCollection;

class GHtmlAttributesCollection extends GCollection
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Добавление элемента(ов) в конец коллекции
     *
     * @param mixed ...$values
     * @return GCollection
     * @since 0.0.2
     * @version 0.0.2
     */
    public function add(...$values): GCollection
    {
        list($name, $value) = $values;
        $this->_items[$name] = $value;
        return $this;
    }

    /**
     * Удаление элемента
     *
     * @param mixed $name
     * @return GCollection
     * @since 0.0.2
     * @version 0.0.2
     */
    public function remove($name): GCollection
    {
        unset($this->_items[$name]);
        return $this;
    }
}
