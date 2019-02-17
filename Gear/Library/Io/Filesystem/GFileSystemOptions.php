<?php

namespace Gear\Library\Io\Filesystem;

use Gear\Traits\GetterTrait;
use Gear\Traits\PropertiesTrait;
use Gear\Traits\SetterTrait;

/**
 * Параметры методов файловой системы
 *
 * @package Gear Framework
 *
 * @property bool append
 * @property string force
 * @property string format
 * @property mixed group
 * @property bool ignoreNewLines
 * @property int|string|null mode
 * @property bool overwrite
 * @property mixed own
 * @property mixed permission
 * @property bool recursive
 * @property bool skip
 * @property mixed user
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GFileSystemOptions
{
    /* Traits */
    use PropertiesTrait;
    use GetterTrait;
    use SetterTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_properties = [
        'append' => false,
        'force' => '',
        'format' => GFileSystem::DEFAULT_SIZEFORMAT,
        'group' => null,
        'ignoreNewLines' => false,
        'mode' => null,
        'overwrite' => false,
        'own' => null,
        'permission' => null,
        'recursive' => false,
        'skip' => false,
        'user' => null,
    ];
    /* Public */

    /**
     * Конструктор
     *
     * @param iterable $options
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __construct(iterable $options = [])
    {
        foreach ($options as $name => $value) {
            $this->$name = $value;
        }
    }
}
