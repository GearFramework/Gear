<?php

namespace eb\components\vendors;

use gear\library\db\GDbStorageComponent;

class GVendorsComponent extends GDbStorageComponent
{
    protected $_factory = [
        'class' => '\eb\models\GVendor',
    ];
    protected $_joins = [
        'vendorStatuses' => [
            'join'=> 'left',
            'link' => ['id' => 'idVendorStatus'],
            'fields' => ['nameVendorStatus'=> 1],
        ],
    ];
    protected $_collectionName = 'vendors';
}