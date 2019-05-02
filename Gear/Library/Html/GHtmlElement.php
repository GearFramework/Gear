<?php

namespace Gear\Library\Html;

use Gear\Library\GModel;
use Gear\Models\Html\GHtmlAttributesCollection;
use Gear\Models\Html\GHtmlClassCollection;

/**
 * Класс html-элементов
 *
 * @package Gear Framework
 *
 * @property iterable attributes
 * @property iterable class
 * @property mixed content
 * @property mixed id
 * @property string name
 * @property string tag
 * @property mixed value
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
abstract class GHtmlElement extends GModel
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_attributes = null;
    protected $_class = null;
    protected $_content = null;
    protected $_id = null;
    protected $_name = null;
    protected $_styles = null;
    protected $_tag = '';
    protected $_value = null;
    /* Public */

    /**
     * Добавление аттрибута
     *
     * @param string $name
     * @param $value
     * @return GHtmlElement
     * @since 0.0.2
     * @version 0.0.2
     */
    public function addAttribute(string $name, $value): GHtmlElement
    {
        if (!$this->_attributes) {
            $this->_attributes = new GHtmlAttributesCollection();
        }
        $this->_attributes->add($name, $value);
        return $this;
    }

    /**
     * Добавление класса к элементу
     *
     * @param string $className
     * @return GHtmlElement
     * @since 0.0.2
     * @version 0.0.2
     */
    public function addClass(string $className): GHtmlElement
    {
        if (!$this->_class) {
            $this->_class = new GHtmlClassCollection([], $this);
        }
        $this->_class->add($className);
        return $this;
    }


    /**
     * Возвращает набор аттрибутов элемента
     *
     * @return iterable|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function attributes(): ?iterable
    {
        return $this->attributes;
    }

    /**
     * Возвращает набор аттрибутов элемента
     *
     * @return iterable|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getAttributes(): ?iterable
    {
        return $this->_attributes;
    }

    public function getClass(): ?iterable
    {
        return $this->_class;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function getName(): ?string
    {
        return $this->_name;
    }

    public function getTag(): string
    {
        return $this->_tag;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function setClass(string $class): GHtmlElement
    {
        return $this->addClass($class);
    }

    public function setContent($content)
    {
        $this->_content = $content;
    }

    public function setId($id): GHtmlElement
    {
        $this->_id = $id;
        return $this;
    }

    public function setName(string $name): GHtmlElement
    {
        $this->name = $name;
        return $this;
    }

    public function setValue($value)
    {
        $this->_value = $value;
    }

    public function style(string $name, $value): GHtmlElement
    {
        if (!$this->_styles) {

        }
        $this->_styles[$name] = $value;
        return $this;
    }
}
