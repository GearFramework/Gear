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
        else
        if ($remove = $this->request->get('remove'))
            return $this->apiRemove($remove);
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
            $result = Core::app()->installer->updateResource($resource);
            if ($result)
                echo "Updated well done\n";
            else
                echo "Updated error\n";
            return $result;
        }
        catch(\Exception $e)
        {
            echo $e->getMessage() . "\n";
        }
        echo "Updated error with exception\n";
        return false;
    }

    public function apiRemove($resource)
    {
        try
        {
            $result = Core::app()->installer->removeResource($resource);
            if ($result)
                echo "Removed well done\n";
            else
                echo "Removed error\n";
            return $result;
        }
        catch(\Exception $e)
        {
            echo $e->getMessage() . "\n";
        }
        echo "Removed error with exception\n";
        return false;
    }

    public function apiHelp()
    {
        echo "Usage: php installer.php --command <resource-type>/<resource-name>\n";
        echo "--install\tInstalling resource <resource-name>\t--install <module|component|plugin|helper>/<resource-name>\n";
        echo "--update\tUpdating resource <resource-name>\t--update <module|component|plugin|helper>/<resource-name>\n";
        echo "--remove\tRemoving resource <resource-name>\t--remove <module|component|plugin|helper>/<resource-name>\n";
    }
}
