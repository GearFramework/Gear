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
            'js' => array
            (
                'class' => '\\gear\\modules\\resource\\plugins\\GJsResource'
            ),
            'css' => array
            (
                'class' => '\\gear\\modules\\resource\\plugins\\GCssResource'
            ),
            'cache' => array
            (
                'class' => '\\gear\\modules\\resource\\plugins\\GResourceCache',
            )
        ),
        'cachePath' => 'temp',
        'salt' => 'Rui43VbthF#',
    );
    /* Public */
    public $storage = 'resources';
    
    public function publicate($file, $wrapper = null, $render = false)
    {
        if (!$wrapper)
            $wrapper = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return $this->p($wrapper)->publicate($file, $render);
    }
    
    public function get($hash, $wrapper)
    {
        return $this->p($wrapper)->get($hash);
    }
    
    public function getContentType($wrapper)
    {
        return $this->p($wrapper)->getContentType();
    }
    
    public function cache($file, array $params = array())
    {
        $hash = $this->getHash($file);
        $params['resource'] = $file;
        return $this->cache->set($hash, $params) ? $hash : null;
    }
    
    public function inCache($hash)
    {
        return $this->cache->exists($hash);
    }
    
    public function getHash($file)
    {
        return md5($file . $this->i('salt'));
    }
}
