<?php

namespace Gear\Library\Html;

use Gear\Library\GModel;
use Gear\Models\Html\GHtmlClassCollection;

/**
 * Класс html-элементов
 *
 * @package Gear Framework
 *
 * @property iterable attributes
 * @property iterable class
 * @property mixed id
 * @property string name
 * @property string tag
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
    protected $_id = null;
    protected $_name = null;
    protected $_styles = null;
    protected $_tag = '';
    /* Public */

    public function addClass(string $className): GHtmlElement
    {
        if (!$this->_class) {
            $this->_class = new GHtmlClassCollection([], $this);
        }
        $this->_class->add($className);
        return $this;
    }

    public function getAttributes(): ?iterable
    {
        return $this->_attributes;
    }

    public function getClass(): ?iterable
    {
        return $this->_class;
    }

    public function getName(): ?string
    {
        return $this->_name;
    }

    public function getTag(): string
    {
        return $this->_tag;
    }

    public function setClass(string $class): GHtmlElement
    {
        return $this->addClass($class);
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

    public function style(string $name, $value): GHtmlElement
    {
        if (!$this->_styles) {

        }
        $this->_styles[$name] = $value;
        return $this;
    }
}
