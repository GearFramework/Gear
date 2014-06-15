<?php

namespace gear\modules\user\process;
use \gear\Core;
use \gear\models\GProcess;

class GAuth extends GProcess
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    public function apiIndex()
    {
        $this->view->render($this->viewPath . '\\loginForm');
    }
    
    public function apiLogin()
    {
        if (Core::m('user')->identity()->isValid())
            die(json_encode(array('error' => 0)));
        else
            die(json_encode(array('error' => 1, 'content' => 'Неверное имя пользователя или пароль')));
    }
    
    public function apiLogout()
    {
        if (Core::m('user')->identity()->isValid())
        {
            Core::m('user')->logout();
            Core::app()->redirect('gear/user/auth');
        }
    }

    public function apiRedirect()
    {
        if ($callback = Core::app()->request->session('callback'))
            Core::app()->redirectUrl($callback);
        else
            Core::app()->redirectUrl('index.php');
    }
}