<?php

namespace Gear\Interfaces;

/**
 * Базовый интерфейс ввода/вывода
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface IoInterface {}

/**
 * Базовый интерфейс уровня файловой системы
 *
 * @package Gear Framework
 *
 * @property int atime
 * @property string basename
 * @property mixed content
 * @property int ctime
 * @property string dirname
 * @property int|string mode
 * @property int mtime
 * @property string name
 * @property IoInterface owner
 * @property string path
 * @property int size
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface FileSystemInterface extends IoInterface
{
    /**
     * Смена прав доступа к элементу
     *
     * @param integer|string|array|GFileSystemOptions $options
     * @return bool
     * @since 0.0.1
     * @version 0.0.2
     */
    public function chmod($options = []): bool;

    /**
     * Меняет владельца (пользователя/группу) элемента файловой системы
     * $this->chown(1001);
     * $this->chown('user');
     * $this->chown('user:group');
     * $this->chown(':group');
     * $this->chown('1001:50');
     * $this->chown(':50');
     * $this->chown(['user' => 1001, 'group' => 50]);
     * и т.п.
     *
     * @param string|int|array|\Gear\Library\Io\Filesystem\GFileSystemOptions $options
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function chown($options = []);

    /**
     * Возвращает true если элемент файловой системы существует
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exists(): bool;

    /**
     * Возвращает true если элемент является файлом, иначе false
     *
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isFile(): bool;

    /**
     * Возвращает true если элемент является папкой, иначе false
     *
     * @return boolean
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isDir(): bool;

    /**
     * Удаление элемента файловой системы
     *
     * @param array $options
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($options = []);
}

/**
 * Интерфейс директорий
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface DirectoryInterface extends FileSystemInterface
{
    public function chdir();

    /**
     * Создание директории
     *
     * @param array|\Gear\Library\Io\Filesystem\GFileSystemOptions $options
     * @return FileSystemInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function create($options = []): FileSystemInterface;
}

/**
 * Базовый интерфейс файлов
 *
 * @package Gear Framework
 *
 * @property string ext
 * @property string extension
 * @property string mime
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface FileInterface extends FileSystemInterface {}