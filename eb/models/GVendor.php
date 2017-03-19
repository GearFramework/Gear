<?php

namespace eb\models;

use gear\library\GModel;

class GVendor extends GModel
{
    public function orders($criteria = [])
    {
        return Core::vendorOrders($this)->find($criteria)->order(['dateOrder' => -1]);
    }
}