<?php

namespace Gear\Library\Io\FileSystem;

use Gear\Core;
use Gear\Interfaces\DirectoryInterface;
use Gear\Interfaces\FileInterface;
use Gear\Interfaces\FileSystemInterface;
use Gear\Interfaces\FileSystemOptionsInterface;
use Gear\Interfaces\IoInterface;
use Gear\Interfaces\ObjectInterface;
use Gear\Library\GEvent;
use Gear\Library\Io\Io;
use JetBrains\PhpStorm\Pure;

/**
 * Класс файловой системы
 *
 * @package Gear Framework 2
 *
 * @property int atime
 * @property string basename
 * @property mixed content
 * @property int ctime
 * @property string dirname
 * @property string filename
 * @property int|string mode
 * @property int mtime
 * @property string name
 * @property IoInterface owner
 * @property string path
 * @property int size
 *
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
abstract class FileSystem extends Io implements FileSystemInterface
{
    /* Traits */
    /* Const */
    const DEFAULT_SIZE_FORMAT = '%01d %s';
    /* Private */
    /* Protected */
    protected static array $model = [
        'dir' => [
            'class' => '\gear\library\io\filesystem\GDirectory',
        ],
        'file' => [
            'class' => '\gear\library\io\filesystem\GFile',
        ],
    ];
    protected static array $sizeUnits = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    /* Public */

    /**
     * Возвращает путь к элементу файловой системы
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function __toString(): string
    {
        return $this->getPath();
    }

    /**
     * Подготовка объекта-параметров из массива
     *
     * @param array|FileSystemOptionsInterface $options
     * @return FileSystemOptionsInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    #[Pure] protected function prepareOptions(array|FileSystemOptionsInterface $options): FileSystemOptionsInterface
    {
        if ($options instanceof FileSystemOptionsInterface) {
            return $options;
        } else {
            return new FileSystemOptions($options);
        }
    }

    /**
     * Создание элемента файловой системы
     *
     * @param array|FileSystemOptionsInterface $options
     * @return false|FileSystemInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function create(array|FileSystemOptionsInterface $options = []): false|FileSystemInterface;

    /**
     * Подготовка и генерация события, возникающего перед созданием элемента файловой системы
     * Возвращает false если создание файла невозможно
     *
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    protected function beforeCreate(FileSystemOptionsInterface $options): bool
    {
        if ($this->beforeCreateCheckCanCreate($options) === false) {
            return false;
        }
        if ($this->exists()) {
            $this->remove();
        }
        return $this->trigger(
            'onBeforeFileSystemCreate',
            new GEvent($this, ['target' => $this, 'options' => $options])
        );
    }

    /**
     * Возвращает true, если возможно создание файла
     *
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 2.0.0
     * @version 2.0.0
     */
    protected function beforeCreateCheckCanCreate(FileSystemOptionsInterface $options): bool
    {
        if ($this->exists() && $options->overwrite === false) {
            return false;
        }
        $directory = $this->beforeCreateGetDestinationDirectory();
        return $directory instanceof DirectoryInterface;
    }

    /**
     * Возвращает директорию в которой будет создаваться элемент
     * Возвращает false если директории не существует, её невозможно создать или произошла
     * ошибка при её создании
     *
     * @return false|DirectoryInterface
     * @since 2.0.0
     * @version 2.0.0
     */
    protected function beforeCreateGetDestinationDirectory(): false|DirectoryInterface
    {
        $directory = $this->getDir();
        if ($directory->exists()) {
            return $directory->isWritable() ? $directory : false;
        }
        if ($directory->create(['recursive' => true]) !== false) {
            return $directory;
        }
        return false;
    }

    /**
     * Генерация события, возникающего после создания элемента файловой системы
     *
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    protected function afterCreate(FileSystemOptionsInterface $options): bool
    {
        if ($options->permission !== null) {
            $this->chmod($options);
        }
        if ($options->own !== null) {
            $this->chown($options);
        }
        return $this->trigger(
            'onAfterFileSystemCreate',
            new Event($this, ['target' => $this, 'options' => $options]),
        );
    }

    /**
     * Удаление элемента файловой системы
     *
     * @param array|FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function remove(array|FileSystemOptionsInterface $options = []): bool;

    /**
     * Подготовка и генерация события, возникающего перед удалением элемента файловой системы
     *
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    protected function beforeRemove(FileSystemOptionsInterface $options): bool
    {
        return $this->trigger(
            'onBeforeFileSystemRemove',
            new GEvent($this, ['target' => $this, 'options' => $options]),
        );
    }

    /**
     * Подготовка и генерация события, возникающего после удаления элемента файловой системы
     *
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 2.0.0
     * @version 2.0.0
     */
    protected function afterRemove(FileSystemOptionsInterface $options): bool
    {
        return $this->trigger(
            'onAfterFileSystemRemove',
            new GEvent($this, ['target' => $this, 'options' => $options]),
        );
    }

    /**
     * Копирование элемента файловой системы
     *
     * @param string|FileSystemInterface $destination
     * @param array|FileSystemOptionsInterface $options
     * @return false|FileSystemInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function copy(
        string|FileSystemInterface $destination,
        array|FileSystemOptionsInterface $options = []
    ): false|FileSystemInterface;

    /**
     * Подготовка и генерация события, возникающего перед копированием элемента файловой системы
     *
     * @param FileSystemInterface $destination
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function beforeCopy(
        FileSystemInterface $destination,
        FileSystemOptionsInterface $options
    ): bool {
        return $this->trigger(
            'onBeforeFileSystemCopy',
            new GEvent($this, ['target' => $this, 'destination' => $destination, 'options' => $options]),
        );
    }

    /**
     * Подготовка и генерация события, возникающего после копирования элемента файловой системы
     *
     * @param FileSystemInterface $destination
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function afterCopy(
        FileSystemInterface $destination,
        FileSystemOptionsInterface $options
    ): bool {
        if ($options->mode !== null) {
            $destination->chmod($options);
        }
        if ($options->own !== null) {
            $destination->chown($options);
        }
        return $this->trigger(
            'onAfterFileSystemCopy',
            new GEvent($this, ['target' => $this, 'destination' => $destination, 'options' => $options]),
        );
    }

    /**
     * Переименование/перемещение элемента файловой системы
     *
     * @param string|FileSystemInterface $destination
     * @param array|FileSystemOptionsInterface $options
     * @return false|FileSystemInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function rename(
        string|FileSystemInterface $destination,
        array|FileSystemOptionsInterface $options = []
    ): false|FileSystemInterface;

    /**
     * Подготовка и генерация события, возникающего перед переименованием элемента файловой системы
     *
     * @param FileSystemInterface $destination
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function beforeRename(FileSystemInterface $destination, FileSystemOptionsInterface $options): bool
    {
        return $this->trigger(
            'onBeforeFileSystemRename',
            new GEvent($this, ['target' => $this, 'destination' => $destination, 'options' => $options]),
        );
    }

    /**
     * Подготовка и генерация события, возникающего после переименованием элемента файловой системы
     *
     * @param FileSystemInterface $destination
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function afterRename(FileSystemInterface $destination, FileSystemOptionsInterface $options): bool
    {
        if ($options->permission !== null) {
            $destination->chmod($options);
        }
        if ($options->own !== null) {
            $destination->chown($options);
        }
        return $this->trigger(
            'onAfterFileSystemRename',
            new GEvent($this, ['target' => $this, 'destination' => $destination, 'options' => $options]),
        );
    }

    /**
     * Возращает timestamp доступа к элементу файловой системы
     *
     * @param null|string $format
     * @return int|string
     * @since 0.0.1
     * @version 0.0.2
     */
    #[Pure] public function atime(?string $format = ''): int|string
    {
        $atime = $this->getAtime();
        return $format ? date($format, $atime) : $atime;
    }

    /**
     * Возращает timestamp доступа к элементу файловой системы
     *
     * @return int
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function getAtime(): int
    {
        return (int)fileatime($this);
    }

    /**
     * Возращает timestamp создания элемента файловой системы
     *
     * @param null|string $format
     * @return int|string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function ctime(?string $format = null): int|string
    {
        $ctime = $this->getCtime();
        return $format ? date($format, $ctime) : $ctime;
    }

    /**
     * Возращает timestamp создания элемента файловой системы
     *
     * @return int
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function getCtime(): int
    {
        return (int)filectime($this);
    }

    /**
     * Возращает timestamp модификации элемента файловой системы
     *
     * @param null|string $format
     * @return int|string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function mtime(?string $format = null): int|string
    {
        $mtime = $this->getMtime();
        return $format ? date($format, $mtime) : $mtime;
    }

    /**
     * Возвращает timestamp модификации элемента файловой системы
     *
     * @return int
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function getMtime(): int
    {
        return (int)filemtime($this);
    }

    /**
     * Смена прав доступа к элементу
     *
     * @param int|string|array|FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function chmod(int|string|array|FileSystemOptionsInterface $options): bool
    {
        if (!is_array($options) && !is_object($options)) {
            $options = ['permission' => $options];
        }
        $options = $this->prepareOptions($options);
        if (is_numeric($options->permission)) {
            $result = chmod($this->path, $options->permission);
        } elseif (is_string($options->permission)) {
            $permission = str_replace(' ', '', $options->permission);
            $permissions = explode(',', $permission);
            foreach ($permissions as $permission) {
                //TODO доделать
            }
            if (strpos($permission, ','))
                $permission = $this->_chmodRelative(explode(',', $permission));
            else if ($permission[0] === 'u' || $permission[0] === 'g' || $permission[0] === 'o' || $permission[0] === 'a')
                $permission = $this->_chmodRelative([$permission]);
            else
                $permission = $this->_chmodTarget($permission);
            $result = chmod($this->path, $permission);
        } else {
            throw self::FileSystemException('Invalid value of permission <{permission}>', ['permission' => $options->permission]);
        }
        return $result;
    }

    /**
     * Возвращает установленные права доступа, в зависимости от значения $mode
     * возвращает либо десятичной системе, либо в восьмеричной, либо в виде строки
     * rwxrwxrwx
     *
     * @param int $mode
     * @return int|string
     * @since 2.0.0
     * @version 2.0.0
     */
    public function mode(int $mode = self::MODE_AS_OCT): int|string
    {
        $perms = $this->getMode();
        return match($mode) {
            self::MODE_AS_DEC    => $perms,
            self::MODE_AS_OCT    => sprintf('%o', $perms),
            self::MODE_AS_STRING => $this->getPermsAsString($perms),
            default              => sprintf('%o', $perms),
        };
    }

    /**
     * Возвращает права доступа в виде строкового
     * представления в виде: rwxrwxrwx
     *
     * @param int $mode
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    protected function getPermsAsString(int $mode): string
    {
        $a = [0xC000 => 's', 0xA000 => 'l', 0x8000 => '-', 0x6000 => 'b', 0x4000 => 'd', 0x2000 => 'c', 0x1000 => 'p'];
        $p = null;
        foreach ($a as $d => $type) {
            if (($mode & $d) == $d) {
                $perm = $type;
                break;
            }
        }
        if (!$perm) {
            $perm = 'u';
        }
        $perm .= (($mode & 0x0100) ? 'r' : '-');
        $perm .= (($mode & 0x0080) ? 'w' : '-');
        $perm .= (($mode & 0x0040) ? (($mode & 0x0800) ? 's' : 'x' ) : (($mode & 0x0800) ? 'S' : '-'));
        $perm .= (($mode & 0x0020) ? 'r' : '-');
        $perm .= (($mode & 0x0010) ? 'w' : '-');
        $perm .= (($mode & 0x0008) ? (($mode & 0x0400) ? 's' : 'x' ) : (($mode & 0x0400) ? 'S' : '-'));
        $perm .= (($mode & 0x0004) ? 'r' : '-');
        $perm .= (($mode & 0x0002) ? 'w' : '-');
        $perm .= (($mode & 0x0001) ? (($mode & 0x0200) ? 't' : 'x' ) : (($mode & 0x0200) ? 'T' : '-'));
        $mode = $perm;
        return '';
    }

    /**
     * Возвращает установленные права доступа
     * По-умолчанию геттер для свойства mode
     *
     * @return int
     * @since 2.0.0
     * @version 2.0.0
     */
    public function getMode(): int
    {
        return (int)fileperms($this);
    }

    /**
     * Меняет владельца (пользователя/группу) элемента файловой системы
     *
     * $this->chown(1001);
     * $this->chown('user');
     * $this->chown('user:group');
     * $this->chown(':group');
     * $this->chown('1001:50');
     * $this->chown(':50');
     * $this->chown(['user' => 1001, 'group' => 50]);
     * и т.п.
     *
     * @param int|string|array|FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function chown(int|string|array|FileSystemOptionsInterface $options = []): bool
    {
        if (is_string($options) || is_numeric($options)) {
            if (($pos = strpos($options, ':')) !== false) {
                $own = explode(':', $options);
                $options = $pos === 0 ? ['group' => $own[0]] : ['user' => $own[0], 'group' => $own[1]];
            } else {
                $options = ['user' => $options];
            }
        }
        $options = $this->_prepareOptions($options);
        if ($options->user !== null) {
            chown($this, $options->user);
        }
        if ($options->group !== null) {
            chgrp($this, $options->group);
        }
    }

    /**
     * Возвращает true если элемент файловой системы существует
     *
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function exists(): bool
    {
        return file_exists($this);
    }

    /**
     * Возвращает полный реальный путь к элементу файловой системы
     *
     * @return false|string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function realpath(): false|string
    {
        return realpath($this->getPath());
    }

    /**
     * Возвращает полный путь к элементу файловой системы
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function path(): string
    {
        return $this->getPath();
    }

    /**
     * Возвращает полный путь к элементу файловой системы
     * Геттер для свойства path
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getPath(): string
    {
        return $this->props('path');
    }

    /**
     * Возвращает имя элемента файловой системы без расширения, если оно имелось
     *
     * @return string
     * @since 0.0.2
     * @version 2.0.0
     */
    #[Pure] public function filename(): string
    {
        return $this->getFilename();
    }

    /**
     * Возвращает имя элемента файловой системы без расширения, если оно имелось
     * Геттер для свойства filename
     *
     * @return string
     * @since 0.0.2
     * @version 2.0.0
     */
    #[Pure] public function getFilename(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
     * Возвращает название с расширением элемента файловой системы
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function basename(): string
    {
        return $this->getBasename();
    }

    /**
     * Возвращает название с расширением элемента файловой системы
     * Геттер для свойства basename
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function getBasename(): string
    {
        return basename($this);
    }

    /**
     * Алиас для метода basename()
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function name(): string
    {
        return $this->getName();
    }

    /**
     * Алиас для метода getBasename()
     * Геттер для свойства name
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function getName(): string
    {
        return $this->getBasename();
    }

    /**
     * Возвращает расширение элемента файловой системы
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function ext(): string
    {
        return $this->getExt();
    }

    /**
     * Возвращает расширение элемента файловой системы
     * Геттер для свойства ext
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function getExt(): string
    {
        return $this->getExtension();
    }

    /**
     * Возвращает расширение элемента файловой системы
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    #[Pure] public function extension(): string
    {
        return $this->getExtension();
    }

    /**
     * Возвращает расширение элемента файловой системы
     * Геттер для свойства extension
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function getExtension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Возвращает mime-тип элемента файловой системы
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function mime(): string
    {
        return self::getMimeByFile($this);
    }

    /**
     * Возвращает mime-тип элемента файловой системы
     * Геттер для свойства mime
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getMime(): string
    {
        return self::getMimeByFile($this);
    }

    /**
     * Возвращает инстанс владельца элемента файловой системы
     *
     * @return DirectoryInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public function dir(): DirectoryInterface
    {
        return $this->getDir();
    }

    /**
     * Возвращает инстанс владельца элемента файловой системы
     * Геттер к свойству dir
     *
     * @return DirectoryInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getDir(): DirectoryInterface
    {
        /** @var DirectoryInterface|null owner */
        if ($this->owner === null) {
            $this->owner = self::factoryDirectory($this->dirname);
        }
        return $this->owner;
    }

    /**
     * Возвращает название директории, в которой находится элемент
     * файловой системы
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function dirname(): string
    {
        return $this->getDirname();
    }

    /**
     * Возвращает название директории, в которой находится элемент
     * файловой системы
     * Геттер для свойства dirname
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function getDirname(): string
    {
        return dirname($this);
    }

    /**
     * Возвращает true, если элемент файловой системы пустой, иначе false
     *
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function isEmpty(): bool;

    /**
     * Возвращает true если элемент является файлом, иначе false
     *
     * @return boolean
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function isFile(): bool
    {
        return is_file($this->path);
    }

    /**
     * Возвращает true если элемент является папкой, иначе false
     *
     * @return boolean
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function isDir(): bool
    {
        return is_dir($this->path);
    }

    /**
     * Возвращает true если элемент является ссылкой, иначе false
     *
     * @return boolean
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function isLink(): bool
    {
        return is_link($this->path);
    }

    /**
     * Возвращает true, если директория открыта, иначе - false
     *
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function isOpened(): bool
    {
        return is_resource($this->handler);
    }

    /**
     * Возвращает true если элемент доступен для чтения
     *
     * @return boolean
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    /**
     * Возвращает true если элемент доступен для записи
     *
     * @return boolean
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    /**
     * Возвращает true если элемент доступен для запуска
     *
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function isExecutable(): bool
    {
        return is_executable($this->path);
    }

//    /**
//     * Возвращает контент элемента файловой системы
//     *
//     * @param mixed $content
//     * @return void
//     * @since 0.0.1
//     * @version 0.0.1
//     */
//    abstract public function setContent($content);
//
//    /**
//     * Установка пути элемента файловой системы
//     *
//     * @param string $path
//     * @return void
//     * @throws \CoreException
//     * @since 0.0.1
//     * @version 0.0.1
//     */
//    public function setPath(string $path): void
//    {
//        $this->props('path', Core::resolvePath($path));
//    }

    /**
     * Возвращает идентификатор группы элемента файловой системы
     *
     * @param bool $asName
     * @return false|int|string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function gid(bool $asName = false): false|int|string
    {
        $gid = $this->getGid();
        if ($gid !== false && $asName) {
            $gid = posix_getgrgid($gid)['name'];
        }
        return $gid;
    }

    /**
     * Возвращает идентификатор группы элемента файловой системы
     *
     * @return false|int
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function getGid(): false|int
    {
        return filegroup($this);
    }

    /**
     * Возвращает идентификатор или имя владельца элемента файловой системы
     *
     * @param bool $asName
     * @return false|int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    #[Pure] public function uid(bool $asName = false): false|int|string
    {
        $uid = $this->getUid();
        if ($uid !== false && $asName) {
            $uid = posix_getpwuid($uid)['name'];
        }
        return $uid;
    }

    /**
     * Возвращает идентификатор владельца элемента файловой системы
     *
     * @return flase|int
     * @since 0.0.1
     * @version 0.0.1
     */
    #[Pure] public function getUid(): false|int
    {
        return fileowner($this);
    }

    /**
     * Возвращает размер элемента файловой системы, в опциях можно указать форматирование
     *
     * @param array|FileSystemOptionsInterface $options
     * @return int|string
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function size(array|FileSystemOptionsInterface $options = []): int|string;

    /**
     * Возвращает размер элемента файловой системы
     * Геттер для свойства size
     *
     * @return int
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function getSize(): int;

    /**
     * Возвращает строковое значение соответствующее типу элемента
     *
     * @return false|string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function type(): false|string
    {
        return filetype($this->getPath());
    }

    /**
     * Возвращает контент элемента файловой системы
     *
     * @param null|callable $contentHandler
     * @return mixed
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function content(?callable $contentHandler = null): mixed;

    /**
     * Возвращает контент элемента файловой системы
     * Геттер для свойства content
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    abstract public function getContent(): string;

    public static function factoryDirectory(string $path): DirectoryInterface
    {
        return new Directory(['path' => $path]);
    }

    public static function factoryFile(string $path): FileInterface
    {
        return new File(['path' => $path]);
    }

    /**
     * Возвращает данные создаваемого объекта
     *
     * @param array $record
     * @param ObjectInterface|null $owner
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getFactoryProperties(array $record = [], ?ObjectInterface $owner = null): array
    {
        $model = static::getModel();
        $factory = null;
        if ($record && isset($record['path'])) {
            if (file_exists($record['path'])) {
                $type = filetype($record['path']);
                if (isset($model[$type])) {
                    $factory = $model[$type];
                }
            } elseif (isset($record['type'])) {
                if (isset($model[$record['type']])) {
                    $factory = $model[$record['type']];
                }
            }
        }
        if (!$factory) {
            throw self::FileSystemException('Unknown filesystem element');
        }
        return array_replace_recursive($factory, $record);
    }

    /**
     * Возвращает отформатированный размер элемента файловой системы
     *
     * @param int $size
     * @param string $format
     * @param string $force
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    #[Pure] public static function formatSize(
        int $size,
        string $format = self::DEFAULT_SIZE_FORMAT,
        string $force = ''
    ): string {
        $force = strtoupper($force);
        if (empty($format)) {
            $format = self::DEFAULT_SIZE_FORMAT;
        }
        $size = max(0, $size);
        $power = array_search($force, self::$sizeUnits);
        if ($power === false) {
            $power = $size > 0 ? floor(log($size, 1024)) : 0;
        }
        return sprintf($format, $size / pow(1024, $power), self::$sizeUnits[$power]);
    }

    /**
     * Возвращает расширение файла по указанному mime-типу
     *
     * @param string $mime
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    #[Pure] public static function getFileExtensionByMime(string $mime): string
    {
        $ext = array_search(strtolower($mime), self::MIME);
        return $ext !== false ? $ext : 'unknown';
    }

    /**
     * Возвращает mime-тип для указанного файла
     *
     * @param string|FileSystemInterface $file
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public static function getMimeByFile(string|FileSystemInterface $file): string
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        return self::MIME[$ext] ?? self::DEFAULT_MIME;
    }
}
