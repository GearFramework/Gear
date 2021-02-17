<?php

namespace gear\helpers;

use Gear\Core;
use Gear\Interfaces\DirectoryInterface;
use Gear\Interfaces\FileInterface;
use Gear\Library\GHelper;
use Gear\Library\Io\Filesystem\GDirectory;
use Gear\Library\Io\Filesystem\GFile;

/**
 * Хелпер для работы с zip-архивами
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class ZipHelper extends GHelper
{
    public static function helpZipDirectory(string $zip, string $directory, int $flags = \ZipArchive::CREATE): FileInterface
    {
        $arc = new \ZipArchive();
        if (!$arc->open($zip, $flags)) {
            throw Core::FileSystemException('Error open zip <{zip}>', ['zip' => $zip]);
        }
        $directory = new GDirectory(['path' => $directory]);
        if ($directory->exists()) {
            self::zipDirectory($arc, $directory);
        }
        $arc->close();
        return new GFile(['path' => $zip]);
    }

    public static function helpZipFile(string $zip, string $file, int $flags = \ZipArchive::CREATE): FileInterface
    {
        $arc = new \ZipArchive();
        if (!$arc->open($zip, $flags)) {
            throw Core::FileSystemException('Error open zip <{zip}>', ['zip' => $zip]);
        }
        $arc->addFile($file, basename($file));
        $arc->close();
        return new GFile(['path' => $zip]);
    }

    private static function zipDirectory(\ZipArchive $arc, DirectoryInterface $dir, string $start = '')
    {
        if ($start) {
            $start .= '/';
        }
        foreach ($dir as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            if (is_dir($item)) {
                self::zipDirectory(
                    $arc,
                    new GDirectory(['path' => $dir->path . '/' . $item]),
                    $start . $dir->basename
                );
            } elseif (is_file($item)) {
                $arc->addFile($dir->path . '/' . $item, $start . $item);
            }
        }
    }
}
