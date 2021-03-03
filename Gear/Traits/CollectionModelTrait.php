<?php

namespace Gear\Traits;

use Gear\Interfaces\ModelsCollectorInterface;

trait CollectionModelTrait
{
    protected $_collector = null;

    public function getCollector(): ?ModelsCollectorInterface
    {
        return $this->_collector;
    }

    public function setCollector(ModelsCollectorInterface $collector): void
    {
        $this->_collector = $collector;
    }
}
