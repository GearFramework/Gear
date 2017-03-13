<?php

namespace gear\modules\user\identifies;

class GSessionIdentity extends GIdentity
{
    public function check()
    {
        $result = false;
        $session = $this->session;
        if ($user = $this->owner->user) {
            $result = true;
        }
        return $result;
    }

    public function getSession()
    {
        return $this->owner->session->validSession;
    }
}