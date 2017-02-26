<?php

namespace gear\plugins\http;

use gear\Core;
use gear\interfaces\IUri;
use gear\library\GModel;
use gear\library\GPlugin;
use gear\traits\http\TUri;

/**
 * Плагин для работы с запросами пользователей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GUriPlugin extends GPlugin implements IUri
{
    /* Traits */
    use TUri;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    /* Public */


    /**
     * TUri constructor.
     *
     * @param array|\Closure $properties
     * @param \gear\interfaces\IObject|null $owner
     * @throws \InvalidArgumentException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __construct($properties = [], $owner = null)
    {
        parent::__construct($properties = [], $owner = null);
        $uri = parse_url($this->uri);
        if ($uri === false)
            throw \InvalidArgumentException("Invalid uri");
        foreach($uri as $name => $value) {
            $this->{'_' . $name} = $value;
        }
        if ($this->_user !== null)
            $this->_userInfo = $this->_user;
        if ($this->_pass !== null)
            $this->_userInfo .= ':' . $this->_pass;
    }
}