<?php

require __DIR__ . '/../Core.php';
\gear\Core::init(array
(
    'modules' => array
    (
        'app' => array
        (
            'class' => array
            (
                'name' => '\\gear\\library\\GApplication',
                'components' => array
                (
                    'process' => array
                    (
                        'class' => '\\gear\\components\\gear\\process\\GProcessComponent',
                        'processes' => array
                        (
                            'application' => array(),
                            'module' => array(),
                            'component' => array(),
                            'plugin'
                        ),
                    ),
                )
            ),
        ),
    ),
));
