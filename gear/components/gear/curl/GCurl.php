<?php

namespace gear\components\gear\curl;
use gear\library\Gcomponent;
use gear\library\GModel;
use gear\traits\TFactory;

class GCurl extends Gcomponent
{
    /* Traits */
    use TFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_defaultProperties =
    [
        'url' => 'http://domain.com',
    ];
    protected $_handler = null;
    protected $_options = [];
    protected $_factory = ['class' => '\gear\components\gear\curl\GResponse'];
    /* Public */

    public function open($url = null, array $options = [])
    {
        $this->_handler = @curl_init($url ?: $this->url);
        if (!$this->_handler)
            $this->e('Error curl init');
        if ($options)
            $this->options = $options;
        foreach($this->options as $name => $value)
        {
            if (!defined($name))
                $name = 'CURLOPT_' . strtoupper($name);
            if (defined($name))
                curl_setopt($this->_handler, constant($name), $value);
        }
        return $this;
    }

    public function close()
    {
        if ($this->isOpened())
        {
            curl_close($this->_handler);
            $this->_handler = null;
            $this->_options = [];
        }
        return $this;
    }

    public function isOpened() { return is_resource($this->_handler); }

    public function setOptions(array $options)
    {
        $this->_options = $options;
        return $this;
    }

    public function getOptions() { return $this->_options; }

    public function setOption($name, $value)
    {
        if (!defined($name))
            $name = 'CURLOPT_' . strtoupper($name);
        if (defined($name))
            curl_setopt($this->_handler, constant($name), $value);
    }

    public function exec()
    {
        if (!$this->isOpened())
            $this->open();
        if (($result = curl_exec($this->_handler)) === false)
            $properties = ['error' => new GModel(['number' => curl_errno($this->_handler), 'message' => curl_error($this->_handler)])];
        else
            $properties = ['error' => false, 'return' => $result];
        return $this->factory($properties);
    }

    public function get($url = null, $params = [], $options = [])
    {
        if ($params && is_array($params))
            $params = http_build_query($params, '', '&');
        $url = ($url ?: $this->url) . ($params ? '?' . $params : '');
        return $this->open($url, array_merge($options, ['HttpGet' => true, 'ReturnTransfer' => true]))->exec();
    }
}