<?php

namespace Gear\Library\Io\Filesystem;

use Gear\Interfaces\FileSystemOptionsInterface;
use Gear\Traits\GetterTrait;
use Gear\Traits\PropertiesTrait;
use Gear\Traits\SetterTrait;

/**
 * Параметры методов файловой системы
 *
 * @package Gear Framework 2
 *
 * @property null|bool append
 * @property null|callable $contentHandler
 * @property null|resource context
 * @property int fileFlags
 * @property null|string force
 * @property null|string format
 * @property null|mixed group
 * @property null|bool ignoreNewLines
 * @property null|int length
 * @property string mode
 * @property int offset
 * @property bool overwrite
 * @property null|int|string|array own
 * @property null|int|string|array permission
 * @property null|bool recursive
 * @property null|bool skip
 * @property null|bool skipEmptyLines
 * @property null|string type
 * @property bool useIncludePath
 * @property null|mixed user
 * @property int whence
 *
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
class FileSystemOptions implements FileSystemOptionsInterface
{
    /* Traits */
    use PropertiesTrait;
    use GetterTrait;
    use SetterTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected array $properties = [
        'append'            => null,
        'contentHandler'    => null,
        'context'           => null,
        'fileFlags'         => 0,
        'force'             => null,
        'format'            => FileSystem::DEFAULT_SIZE_FORMAT,
        'group'             => null,
        'ignoreNewLines'    => null,
        'length'            => null,
        'mode'              => 'r',
        'offset'            => 0,
        'overwrite'         => false,
        'own'               => null,
        'permission'        => null,
        'recursive'         => null,
        'skip'              => null,
        'skipEmptyLines'    => null,
        'type'              => null,
        'useIncludePath'    => false,
        'user'              => null,
        'whence'            => SEEK_SET,
    ];
    /* Public */

    /**
     * Конструктор
     *
     * @param iterable $options
     * @since 0.0.1
     * @version 2.0.0
     */
    public function __construct(iterable $options = [])
    {
        foreach ($options as $name => $value) {
            $this->$name = $value;
        }
    }

    public function contentHandler(mixed $content): mixed
    {
        return $this->properties['contentHandler']($content);
    }
}
