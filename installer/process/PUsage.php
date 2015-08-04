<?php

namespace gear\installer\process;
use gear\Core;
use gear\models\GProcess;

class PUsage extends GProcess
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_viewPath = '\gear\installer\views';
    /* Public */

    public function apiIndex()
    {
        if ($install = $this->request->get('install'))
            return $this->apiInstall($install);
        else
        if ($update = $this->request->get('update'))
            return $this->apiUpdate($update);
        return $this->apiHelp();
        //$this->view->render('usage');
    }

    public function apiInstall($resource)
    {
        try
        {
            $result = Core::app()->installer->installResource($resource);
            if ($result)
                echo "Installed well done\n";
            else
                echo "Installed error\n";
            return $result;
        }
        catch(\Exception $e)
        {
            echo $e->getMessage() . "\n";
        }
        echo "Installed error with exception\n";
        return false;
    }

    public function apiUpdate($resource)
    {
        try
        {
            return Core::app()->installer->updateResource($resource);
        }
        catch(\Exception $e)
        {
            echo $e->getMessage() . "\n";
        }
        return false;
    }

    public function apiHelp()
    {

    }
}
