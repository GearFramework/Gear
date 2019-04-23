<?php

namespace Gear\Models\Ffmpeg;

use Gear\Interfaces\DependentInterface;
use Gear\Library\Io\Filesystem\GFile;

class FFMpegMovie extends GFile implements DependentInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    public function getDuration()
    {
        $out = shell_exec($this->shellCommand . ' -i ' . $this->path);
    }
}