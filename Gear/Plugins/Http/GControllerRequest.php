<?php

namespace Gear\Plugins\Http;

use Gear\Interfaces\ControllerInterface;
use Gear\Interfaces\ControllerRequestInterface;
use Gear\Interfaces\RequestDataInterface;
use Gear\Interfaces\RequestInterface;
use Gear\Traits\Factory\FactoryTrait;

/**
 * Плагин-обёртка для обработки и подготовки данных запросов для контроллеров
 *
 * @package Gear Framework
 *
 * @property ControllerInterface controller
 * @property RequestDataInterface modelRequest
 * @property ControllerInterface owner
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class GControllerRequest extends GRequest implements ControllerRequestInterface
{
    /* Traits */
    use FactoryTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_model = [
        'class' => '\Gear\Models\Http\GRequestData',
    ];
    /** @var null|RequestDataInterface $_modelRequest */
    protected $_modelRequest = null;
    protected $_request = null;
    /* Public */

    protected function _filterProperties(array $properties): array
    {
        if (array_key_exists('class', $properties)) {
            unset($properties['class']);
        }
        return $properties;
    }

    protected function getModelRequest($data): RequestDataInterface
    {
        if (!$this->_modelRequest) {
            $model = $this->_model;
            if (isset($this->controller->requestModels[$this->controller->apiRequest])) {
                $model = $this->controller->requestModels[$this->controller->apiRequest];
            }
            $this->_modelRequest = $this->factory(array_merge($data, $model));
        }
        return $this->_modelRequest;
    }

    /**
     * Возвращает контроллер, к которому привязан плагин
     *
     * @return ControllerInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getController(): ControllerInterface
    {
        return $this->owner;
    }

    public function getData(array &$data, string $name = '', $value = null, $default = null)
    {
        $model = $this->getModelRequest($data);
        if (empty($name)) {
            return $model;
        } else {
            try {
                $val = $model->$name;
                if ($val === null) {
                    $val = $default;
                }
            } catch (\Exception $e) {
                $val = $default;
            }
            return $val;
        }
    }

    public function getFactoryProperties(array $properties = []): array
    {
        $model = $this->_model;
        if (isset($this->controller->requestModels[$this->controller->apiRequest])) {
            $model = $this->controller->requestModels[$this->controller->apiRequest];
        }
        return array_replace_recursive($model, $this->_filterProperties($properties));
    }
}