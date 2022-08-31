<?php

namespace Gear\Interfaces;

/**
 * Базовый интерфейс ввода/вывода
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface IoInterface {}

/**
 * Интерфейс объектов с параметрами, для передачи в некоторые методы IO-классов
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface FileSystemOptionsInterface {}

/**
 * Базовый интерфейс уровня файловой системы
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
interface FileSystemInterface extends IoInterface
{
    /**
     * Создание элемента файловой системы
     *
     * @param array|FileSystemOptionsInterface $options
     * @return false|FileSystemInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function create(array|FileSystemOptionsInterface $options = []): false|FileSystemInterface;

    /**
     * Удаление элемента файловой системы
     *
     * @param array|FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove(array|FileSystemOptionsInterface $options = []): bool;

    /**
     * Копирование элемента файловой системы
     *
     * @param string|FileSystemInterface $destination
     * @param array|FileSystemOptionsInterface $options
     * @return FileSystemInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function copy(
        string|FileSystemInterface $destination,
        array|FileSystemOptionsInterface $options = []
    ): FileSystemInterface;

    /**
     * Переименование/перемещение элемента файловой системы
     *
     * @param string|FileSystemInterface $destination
     * @param array|FileSystemOptionsInterface $options
     * @return FileSystemInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function rename(
        string|FileSystemInterface $destination,
        array|FileSystemOptionsInterface $options = []
    ): FileSystemInterface;

    /**
     * Смена прав доступа к элементу
     * Примеры значений
     *    0666
     *    +x
     *    g+rw
     *    rwxr-wr--
     *
     * @param int|string $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function chmod(int|string $options): bool;

    /**
     * Меняет владельца (пользователя/группу) элемента файловой системы
     * Примеры значений
     *    chown(1001);
     *    chown('user');
     *    chown('user:group');
     *    chown(':group');
     *    chown('1001:50');
     *    chown(':50');
     *    chown(['user' => 1001, 'group' => 50]);
     *    и т.п.
     *
     * @param string|int|array $options
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function chown(string|int|array $options): bool;

    /**
     * Возвращает true если элемент файловой системы существует
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exists(): bool;

    /**
     * Возвращает имя элемента файловой системы без расширения, если оно имелось
     *
     * @return string
     * @since 0.0.2
     * @version 0.0.2
     */
    public function filename(): string;

    /**
     * Возвращает имя элемента файловой системы без расширения, если оно имелось
     * Геттер для свойства filename
     *
     * @return string
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getFilename(): string;

    /**
     * Возвращает название с раширением элемента файловой системы
     *
     * @return string
     */
    public function basename(): string;

    /**
     * Возвращает название с раширением элемента файловой системы
     * Геттер для свойства basename
     *
     * @return string
     */
    public function getBasename(): string;

    /**
     * Возвращает название директории, в которой находится элемент
     * файловой системы
     *
     * @return string
     */
    public function dirname(): string;

    /**
     * Возвращает название директории, в которой находится элемент
     * файловой системы
     * Геттер для свойства dirname
     *
     * @return string
     */
    public function getDirname(): string;

    /**
     * Возвращает true если элемент является файлом, иначе false
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isFile(): bool;

    /**
     * Возвращает true если элемент является папкой, иначе false
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isDir(): bool;
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
    /**
     * Смена на текущую директорию, вернёт true в случае успеха
     * Если в качестве параметра передена другая директория
     * то смена текущей произойдёт в неё и в этом случае будет
     * возвращён объект этой директории не зависимо от результата смены
     *
     * @param string|DirectoryInterface|null $directory
     * @return bool|DirectoryInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public function chdir(string|DirectoryInterface|null $directory = null): bool|DirectoryInterface;

    /**
     * Создание директории
     *
     * @param array|FileSystemOptionsInterface $options
     * @return bool|FileSystemInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function mkdir(array|FileSystemOptionsInterface $options = []): bool|DirectoryInterface;
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
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface FileInterface extends FileSystemInterface
{
    /**
     * Возвращает расширение файла
     *
     * @return string
     */
    public function ext(): string;

    /**
     * Возвращает расширение файла
     * Геттер для свойства ext
     *
     * @return string
     */
    public function getExt(): string;

    /**
     * Возвращает расширение файла
     *
     * @return string
     */
    public function extension(): string;

    /**
     * Возвращает расширение файла
     * Геттер для свойства extension
     *
     * @return string
     */
    public function getExtension(): string;

    /**
     * Возвращает mime-тип файла
     *
     * @return string
     */
    public function mime(): string;

    /**
     * Возвращает mime-тип файла
     * Геттер для свойства mime
     *
     * @return string
     */
    public function getMime(): string;
}
