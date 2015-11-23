<?php

namespace gear\builder\components;

use \gear\Core;
use \gear\library\GComponent;

class GBuilderComponent extends GComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_manifest = array();
    /* Public */
    
    public function createApplication(array $global, array $manifest)
    {
        $this->props($global);
        $this->_prepareManifest($manifest);
        return $this->_runScenario();
    }
    
    public function param($name)
    {
        return isset($this->_properties[$name]) ? $this->_properties[$name] : null;
    }
    
    protected function _runScenario()
    {
        if (!isset($this->scenario) || !is_array($this->scenario))
            return false;
        foreach($this->scenario as $step)
        {
            $command = array_shift($step);
            call_user_func_array(array($this, '_' . $command), $step);
        }
        return true;
    }
    
    protected function _prepareManifest($manifest, $section = null)
    {
        foreach($manifest as $op => $value)
        {
            if ($op[0] === ':')
            {
                $sectionName = $section ? $section . '.' . substr($op, 1) : substr($op, 1);
                if (is_array($value))
                    $this->_properties[$sectionName] = $value;
                else
                {
                    $value = $this->_prepareValue($value);
                    if (is_array($value))
                    {
                        foreach($value as $partName => $partValue)
                            $this->_properties[$sectionName . '.' . $partName] = $partValue;
                    }
                    else
                        $this->_properties[$sectionName] = $value;
                }
            }
            else
            if ($op[0] === '#')
            {
                $sectionName = $section ? $section . '.' . substr($op, 1) : substr($op, 1);
                $this->_prepareManifest($value, $sectionName);
            }
        }
    }
    
    protected function _prepareValue($value, $section = null)
    {
        if (preg_match('/\{(\#)([a-zA-Z0-9_\.]+)\}/', $value, $result))
        {
            $propertyName = $section ? $section . '.' . $result[2] : $result[2];
            $result = array();
            foreach($this->_properties as $name => $propertyValue)
            {
                if (preg_match('/^' . preg_quote($propertyName) . '\./', $name))
                {
                    $part = str_replace($propertyName . '.', '', $name);
                    $result[$part] = $propertyValue;
                }
            }
            return $result;
        }
        while(preg_match('/\{(\:)([a-zA-Z0-9_\.]+)\}/', $value, $result))
        {
            $propertyName = $section ? $section . '.' . $result[2] : $result[2];
            if (isset($this->_properties[$propertyName]) && !is_array($this->_properties[$propertyName]))
                $value = str_replace('{:' . $result[2] . '}', $this->_properties[$propertyName], $value);
            else
                $value = str_replace('{:' . $result[2] . '}', '', $value);
        }
        return $value;
    }
    
    public function prepareValue($value)
    {
        while(preg_match('/\{(\:)([a-zA-Z0-9_\.]+)\}/', $value, $result))
        {
            $propertyName = $result[2];
            if (isset($this->_properties[$propertyName]) && !is_array($this->_properties[$propertyName]))
                $value = str_replace('{:' . $result[2] . '}', $this->_properties[$propertyName], $value);
            else
                $value = str_replace('{:' . $result[2] . '}', '', $value);
        }
        return $value;
    }
    
    protected function _mkdir($folder, $mode = 0776)
    {
        $folder = $this->_prepareValue($folder);
        echo "+ Create folder $folder\n";
        mkdir($this->_prepareValue($folder), $mode);
    }
    
    protected function _mkfile($templatePath, $filePath, $params = array())
    {
        $templatePath = $this->_prepareValue($templatePath);
        $filePath = $this->_prepareValue($filePath);
        echo "+ Create file from template $templatePath to $filePath\n";
        $content = $this->view->render($templatePath, $params, true);
        $content = str_replace('&lt;?php', '<?php', $content);
        file_put_contents($filePath, $content);
    }
}