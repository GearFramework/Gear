<?php

namespace gear\validators;

use gear\Core;
use gear\interfaces\IObject;

class GObjectValidator
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_rules = [
        'id' => [
            'isNumeric' => [
                'throws' => false,
                'errors' => true,
                'cast' => true,
            ],
        ],
    ];
    /* Public */

    public function __invoke()
    {
        return $this->validate(... func_get_args());
    }

    public function validate() {}

    public function validateProperty(IObject $object, $name)
    {
        $value = $object->$name;
        $result = true;
        if (isset($this->_rules[$name])) {
            foreach($this->_rules[$name] as $ruleName => $rule) {
                if (method_exists($this, $ruleName)) {
                    $value = $this->$ruleName($value, $rule);
                }
            }
        }
        return $result;
    }

    public function isNumeric($value, array $rule = [], $default = null)
    {
        $errors = [];
        foreach($rule as $name => $v) {
            switch ($name) {
                case 'cast':
                    if ($v == true) {
                        $value = (int)$value;
                    } else if (isset($rule['throws']) && $rule['throws']) {
                        throw Core::exceptionValidator('Value mast be a integer');
                    }
                    break;
                case 'min':
                    if ($value < $v) {
                        if (isset($rule['throws']) && $rule['throws'])
                            throw Core::exceptionValidator('Value <:value> less min value <:min>', ['value' => $value, 'min' => $v]);
                        if (isset($rule['errors']) && $rule['errors'])
                            $errors[] = sprintf('Value %d less min value %d', $value, $v);
                    }
                case 'max':
                    if ($value > $v && (isset($rule['throws']) && $rule['throws'])) {
                        throw Core::exceptionValidator('Value <:value> overflow max value <:max>', ['value' => $value, 'max' => $v]);
                    }
            }
        }
        return $value;
    }
}
