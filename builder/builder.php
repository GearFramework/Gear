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
                            'application' => array('class' => '\\gear\\builder\\process\\GApplication'),
                            'module' => array('class' => '\\gear\\builder\\process\\GModule'),
                            'component' => array('class' => '\\gear\\builder\\process\\GComponent'),
                        ),
                    ),
                )
            ),
            '_namespace' => '\\gear\\builder',
        ),
    ),
    'components' => array
    (
        'templater' => array('class' => '\\gear\\builder\\components\\GBuilderComponent'),
    ),
));
\gear\Core::app()->run();