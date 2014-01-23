<?php

namespace gear\modules\resource;
use \gear\Core;
use \gear\library\GModule;
use \gear\library\GEvent;
use \gear\library\GException;

class GResource extends GModule
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'plugins' => array
        (
            'client' => array
            (
                'class' => '\\gear\\modules\\resource\\plugins\\GClientResource'
            ),
            'cache' => array
            (
                'class' => '\\gear\\modules\\resource\\plugins\\GResourceCache',
            )
        ),
        'cachePath' => 'temp',
        'storages' => array
        (
            'resources'
        ),
        'salt' => 'Rui43VbthF#',
    );
    /* Public */
    public $storages = array();
    
    public function getResource($file, $wrapper = 'client', $render = false)
    {
        return $this->p($wrapper)->getResource($file, $render);
    }
    
    public function cache($file)
    {
        $hash = $this->getHash($file);
        $tempPath = Core::resolvePath($this->i('cachePath') . '/' . $hash);
        file_put_contents($tempPath, $file);
        return $hash;
    }
    
    public function inCache($file)
    {
        return file_exists(Core::resolvePath($this->i('cachePath') . '/' . (preg_match('/^[a-f0-9]{32}$/', $file) ? $file : $this->getHash($file))));
    }
    
    public function getHash($file)
    {
        return md5($file . $this->i('salt'));
    }
    
    public function getPath($file)
    {
        $tempPath = Core::resolvePath($this->i('cachePath') . '/' . $this->getHash($file));
        return file_exists($tempPath) ? file_get_contents($tempPath) : null;
    }
}
