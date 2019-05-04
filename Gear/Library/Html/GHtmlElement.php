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
    protected $_children = [];
    protected $_class = null;
    protected $_content = null;
    protected $_id = null;
    protected $_name = null;
    protected $_styles = null;
    protected $_tag = '';
    protected $_value = null;
    /* Public */

    public function __call(string $name, array $args)
    {
        $element = \Html::$name(...$args);
        $this->addChild($element);
        return $element;
    }

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

    public function addChild(GHtmlElement $element): GHtmlElement
    {
        $this->_children[] = $element;
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
            $this->_class = new GHtmlClassCollection();
        }
        $this->_class->add($className);
        return $this;
    }


    /**
     * Возвращает набор аттрибутов элемента
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function attributes(?string $name = null, $value = null)
    {
        $ret = null;
        if (!$name && !$value) {
            return $this->attributes;
        } else {
            if ($value) {
                $this->attributes->$name = $value;
            } else {
                $ret = $this->attributes->$name;
            }
        }
        return $ret;
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

    /**
     * Возвращает набор классов
     *
     * @return iterable|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getClass(): ?iterable
    {
        return $this->_class;
    }

    /**
     * Возвращает содержимое элемента
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Возвращает значение аттрибута id
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getId()
    {
        return $this->attributes('id');
    }

    /**
     * Возвращает значение аттрибута name
     *
     * @return null|string
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getName(): ?string
    {
        return $this->attributes('name');
    }

    /**
     * Возвращает название html-тэга
     *
     * @return string
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getTag(): string
    {
        return $this->_tag;
    }

    /**
     * Возвращает значение аттрибута value
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getValue()
    {
        return $this->attributes('value');
    }

    public function setClass(string $class): GHtmlElement
    {
        return $this->addClass($class);
    }

    public function setContent($content)
    {
        $this->_content = $content;
    }

    public function setId($id)
    {
        $this->attributes('id', $id);
    }

    public function setName(string $name)
    {
        $this->attributes('name', $name);
    }

    public function setValue($value)
    {
        $this->attributes('value', $value);
    }

    public function style(string $name, $value): GHtmlElement
    {
        if (!$this->_styles) {

        }
        $this->_styles[$name] = $value;
        return $this;
    }
}
