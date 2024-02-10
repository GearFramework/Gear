<?php

namespace Gear\Library\Io\Filesystem;

use Gear\Core;
use Gear\Interfaces\DirectoryInterface;
use Gear\Interfaces\FileInterface;
use Gear\Interfaces\FileSystemInterface;
use Gear\Interfaces\FileSystemOptionsInterface;
use JetBrains\PhpStorm\Pure;

/**
 * Класс файлов
 *
 * @package Gear Framework 2
 *
 * @property mixed content
 * @property string ext
 * @property string extension
 * @property string mime
 * @property string path
 * @property int size
 *
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
class File extends FileSystem implements FileInterface \IteratorAggregate
{
    /* Traits */
    /* Const */
    /* Private */
    private array|FileSystemOptionsInterface $options = [
        'mode' => 'r',
    ];
    /* Protected */
    /* Public */

    /**
     * Открывает файл для операций ввода/вывода
     *
     * @param array|FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function open(array|FileSystemOptionsInterface $options = []): bool
    {
        if ($this->isOpened()) {
            return true;
        }
        $options = $this->prepareOptions($options);
        $result = fopen($this, $options->mode);
        if ($result === false) {
            return false;
        }
        $this->handler = $result;
        $this->options = $options;
        return true;
    }

    /**
     * Закрывает файл
     *
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function close(): bool
    {
        if ($this->isOpened() === false) {
            return true;
        }
        $result = fclose($this->handler);
        $this->handler = null;
        return $result;
    }

    /**
     * Считывает символ из файла
     *
     * @return false|string
     * @since 2.0.0
     * @version 2.0.0
     */
    public function getc(): false|string
    {
        if ($this->checkOpened() === false) {
            return false;
        }
        return fgetc($this->handler);
    }

    /**
     * Читает строку из файла
     *
     * @param int|null $length
     * @return false|string
     * @since 2.0.0
     * @version 2.0.0
     */
    public function gets(?int $length = null): false|string
    {
        if ($this->checkOpened() === false) {
            return false;
        }
        return fgets($this->handler, $length);
    }

    /**
     * Запись данных в файл. Возвращает кол-во записанных байт или false в случае
     * неудачной записи
     *
     * @param string $data
     * @param int|null $length
     * @return false|int
     * @since 0.0.1
     * @version 2.0.0
     */
    public function puts(string $data, ?int $length = null): false|int
    {
        if ($this->checkOpened() === false) {
            return false;
        }
        return fputs($this->handler, $data, $length);
    }

    /**
     * Чтение из файловой дескриптора, не больше чем $length байт
     *
     * @param int $length
     * @return false|string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function read(int $length = self::DEFAULT_LENGTH_READ): false|string
    {
        if ($this->checkOpened() === false) {
            return false;
        }
        return fread($this->handler, $length);
    }

    /**
     * Запись данных в файл. Возвращает кол-во записанных байт или false в случае
     * неудачной записи
     *
     * @param int|string|array $data
     * @param int|null $length
     * @return false|int
     * @since 0.0.1
     * @version 2.0.0
     */
    public function write(int|string|array $data, ?int $length = null): false|int
    {
        if (!$this->isOpened()) {
            if ($this->open() === false) {
                return false;
            }
        }
        return fwrite($this->handler, $data, $length);
    }

    /**
     * Смещение внутреннего указателя
     *
     * @param int $offset
     * @param int $whence
     * @return int
     * @since 0.0.1
     * @version 2.0.0
     */
    public function seek(int $offset, int $whence = SEEK_SET): int
    {
        if ($this->checkOpened() === false) {
            return 0;
        }
        return fseek($this->handler, $offset, $whence);
    }

    /**
     * Проверяет, достигнут ли конец файла
     *
     * @return bool
     */
    public function eof(): bool
    {
        if ($this->isOpened() === false) {
            return true;
        }
        return feof($this->handler);
    }

    /**
     * Проверка на открытость файла, если файл закрыт, то пытается его
     * открыть либо с параметрами по-умолчанию, либо с переданными
     * в качестве аргумента
     *
     * @param null|array|FileSystemOptionsInterface $options
     * @return bool
     */
    private function checkOpened(null|array|FileSystemOptionsInterface $options = null): bool
    {
        if ($this->isOpened()) {
            return true;
        }
        if ($options === null) {
            $options = $this->options;
        }
        $options = $this->prepareOptions($options);
        return $this->open($options);
    }

    /**
     * Создание файла
     * В случае неудачи генерирует исключение Gear\Exceptions\FileSystemException
     *
     * @param array|FileSystemOptionsInterface $options
     * @return false|FileSystemInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public function create(array|FileSystemOptionsInterface $options = []): false|FileSystemInterface
    {
        $options = $this->prepareOptions($options);
        if ($this->beforeCreate($options) === false) {
            return false;
        }
        if (touch($this) === false) {
            return false;
        }
        $this->afterCreate($options);
        return $this;
    }

    /**
     * Удаление элемента файловой системы
     *
     * @param array|FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    public function remove(array|FileSystemOptionsInterface $options = []): bool
    {
        $options = $this->prepareOptions($options);
        if ($this->beforeRemove($options) === false) {
            return false;
        }
        $result = @unlink($this->getPath(), $options->context);
        return $result;
    }

    /**
     * Подготовка и генерация события, возникающего перед удалением файла
     *
     * @param FileSystemOptionsInterface $options
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    protected function beforeRemove(FileSystemOptionsInterface $options): bool
    {
        if ($this->exists() === false || $this->isWritable() === false) {
            return false;
        }
        return parent::beforeRemove($options);
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
    public function copy(
        string|FileSystemInterface $destination,
        array|FileSystemOptionsInterface $options = []
    ): false|FileSystemInterface {
        $options = $this->prepareOptions($options);
        $destinationFile = $this->getDestinationFile($destination);
        if ($this->beforeCopy($destinationFile, $options) === false) {
            return false;
        }
        if (copy($this->getPath(), $destinationFile->getPath()) === false) {
            return false;
        }
        $this->afterCopy($destinationFile, $options);
        return $destinationFile;
    }

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
        if ($destination->exists() && $options->overwrite === false) {
            return false;
        }
        $directory = $this->beforeGetDestinationDirectory($destination);
        if ($directory === false) {
            return false;
        }
        return parent::beforeCopy($destination, $options);
    }

    /**
     * Возвращает директорию в которую будет происходить копирование
     * Если директории не существует, то пытается её создать
     * В случае неудачи возвращает false
     *
     * @param FileSystemInterface $destination
     * @return false|DirectoryInterface
     * @since 2.0.0
     * @version 2.0.0
     */
    private function beforeGetDestinationDirectory(FileSystemInterface $destination): false|DirectoryInterface
    {
        $directory = $destination->dir();
        if ($directory->exists()) {
            return $directory;
        }
        if ($directory->create(['recursive' => true]) !== false) {
            return $directory;
        }
        return false;
    }

    /**
     * Возвращает файл назначения для операций копирования, переименования
     *
     * @param string|DirectoryInterface|FileInterface $destination
     * @return FileInterface
     * @since 2.0.0
     * @version 2.0.0
     */
    private function getDestinationFile(string|DirectoryInterface|FileInterface $destination): FileInterface
    {
        if ($destination instanceof DirectoryInterface || is_dir($destination)) {
            return $this->getDestinationFileByDirectory($destination);
        }
        if ($destination instanceof FileInterface || is_file($destination)) {
            return $this->getDestinationFileByFile($destination);
        }
        return $this->getDestinationFileByDirectory($destination);
    }

    /**
     * Возвращает объект файл назначения для операций копирования, переименования на основе
     * директории назначения
     *
     * @param string|DirectoryInterface $destination
     * @return FileInterface
     * @since 2.0.0
     * @version 2.0.0
     */
    private function getDestinationFileByDirectory(string|DirectoryInterface $destination): FileInterface
    {
        return self::factoryFile("{$destination}/{$this->getBasename()}");
    }

    /**
     * Возвращает объекта файла назначения для операций копирования, переименования на основе
     * указанного файла назначения
     *
     * @param string|FileInterface $destination
     * @return FileInterface
     * @since 2.0.0
     * @version 2.0.0
     */
    private function getDestinationFileByFile(string|FileInterface $destination): FileInterface
    {
        return is_string($destination)
            ? self::factoryFile("{$destination}/{$this->getBasename()}")
            : $destination;
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
    public function rename(
        string|FileSystemInterface $destination,
        array|FileSystemOptionsInterface $options = []
    ): false|FileSystemInterface {
        $options = $this->prepareOptions($options);
        $destinationFile = $this->getDestinationFile($destination);
        if ($this->beforeRename($destinationFile, $options) === false) {
            return false;
        }
        if (rename($this->getPath(), $destinationFile->getPath()) === false) {
            return false;
        }
        $this->afterRename($destinationFile, $options);
        return $destinationFile;
    }

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
        if ($destination->exists() && $options->overwrite === false) {
            return false;
        }
        $directory = $this->beforeGetDestinationDirectory($destination);
        if ($directory === false) {
            return false;
        }
        return parent::beforeRename($destination, $options);
    }

    /**
     * Возвращает контент файла
     *
     * @param null|callable $contentHandler
     * @return mixed
     * @since 0.0.1
     * @version 2.0.0
     */
    public function content(?callable $contentHandler = null): mixed
    {
        $data = $this->getContent();
        return is_callable($contentHandler) ? $contentHandler($data) : $data;
    }

    /**
     * Возвращает контент файла
     * Геттер для свойства content
     *
     * @return string
     * @since 0.0.1
     * @version 2.0.0
     */
    public function getContent(): string
    {
        $data = $this->get();
        if ($data === false) {
            $data = '';
        }
        return $data;
    }

    /**
     * Возвращает контент элемента файловой системы
     *
     * @param mixed $content
     * @return void
     * @since 0.0.1
     * @version 2.0.0
     */
    public function setContent(mixed $content): void
    {
        $this->put($content);
    }

    /**
     * Возвращает контент файла
     *
     * @param array|FileSystemOptionsInterface $options
     * @return mixed
     * @see file_get_contents()
     * @since 2.0.0
     * @version 2.0.0
     */
    public function get(array|FileSystemOptionsInterface $options = []): mixed
    {
        $options = $this->prepareOptions($options);
        $data = @file_get_contents(
            $this->getPath(),
            $options->useIncludePath,
            $options->context,
            $options->offset,
            $options->length
        );
        if (is_callable($options->contentHandler)) {
            $handler = $options->contentHandler;
            $data = $handler($data);
        }
        return $data;
    }

    /**
     * Сохраняет контент в файл
     *
     * @param mixed $data
     * @param array|FileSystemOptionsInterface $options
     * @return false|int
     * @see file_put_contents()
     * @since 2.0.0
     * @version 2.0.0
     */
    public function put(mixed $data, array|FileSystemOptionsInterface $options = []): false|int
    {
        $options = $this->prepareOptions($options);
        if (is_array($data) && is_callable($options->contentHandler)) {
            $handler = $options->contentHandler;
            $data = $handler($data);
        }
        return @file_put_contents($this->getPath(), $data, $options->fileFlags, $options->context);
    }

    /**
     * Возвращает массив строк из файла
     *
     * @param array|FileSystemOptionsInterface  $options
     * @return false|array
     * @since 0.0.1
     * @version 2.0.0
     */
    public function file(array|FileSystemOptionsInterface $options = []): false|array
    {
        $options = $this->prepareOptions($options);
        return file($this->getPath(), $options->fileFlags, $options->context);
    }

    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \ArrayIterator An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->file(['ignoreNewLines' => true]));
    }

    /**
     * Возвращает true, если файл пустой, иначе false
     *
     * @return bool
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function isEmpty(): bool
    {
        return (bool)filesize($this);
    }

    /**
     * Возвращает размер элемента файловой системы
     * $format может принимать строку в котором возвратить размер файла
     *      '%01d %s'
     * или массив
     * Array (
     *      'format' => '%01d %s',
     *      'force' => 'kb'
     * )
     * @param array|FileSystemOptionsInterface $options
     * @return int|string
     * @since 0.0.1
     * @version 2.0.0
     */
    #[Pure] public function size(array|FileSystemOptionsInterface $options = []): int|string
    {
        $options = $this->prepareOptions($options);
        $size = filesize($this);
        if ($options->format) {
            $size = self::formatSize($size, $options->format, $options->force);
        }
        return $size;
    }
}
