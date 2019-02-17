<?php

namespace Gear\Plugins\Http;

use Gear\Library\GPlugin;
use Gear\Traits\Http\UriTrait;

/**
 * Плагин для работы с запросами пользователей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GUri extends GPlugin
{
    /* Traits */
    use UriTrait;
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * GUri constructor.
     *
     * @param array|\Closure $properties
     * @param \Gear\Interfaces\ObjectInterface|null $owner
     * @throws \InvalidArgumentException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function __construct($properties = [], $owner = null)
    {
        parent::__construct($properties, $owner);
        $uri = parse_url($this->uri);
        if ($uri === false)
            throw \InvalidArgumentException("Invalid uri");
        foreach ($uri as $name => $value) {
            $this->{'_' . $name} = $value;
        }
        if ($this->_user !== null)
            $this->_userInfo = $this->_user;
        if ($this->_pass !== null)
            $this->_userInfo .= ':' . $this->_pass;
    }
}
