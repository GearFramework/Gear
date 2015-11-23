<?php

namespace gear\library;

use gear\Core;
use gear\library\GService;
use gear\library\GException;
use gear\library\TEvents;
use gear\library\TBehaviors;
use gear\library\TPlugins;
use gear\interfaces\IComponent;

/** 
 * Класс компонентов
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.08.2013
 * @php 5.4.x or higher
 * @release 1.0.0
 */
abstract class GComponent extends GService implements IComponent
{
    /* Traits */
    use TEvents;
    use TBehaviors;
    use TPlugins;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config =
    [
        'events' => [],
        'behaviors' => [],
        'plugins' =>
        [
            'view' => ['class' => '\gear\plugins\gear\GView'],
        ],
    ];
    protected static $_init = false;
    /* Public */


    /**
     * Копирование компонента
     *
     * @access public
     * @return void
     */
    public function __clone() { parent::__clone(); }
}
