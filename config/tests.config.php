<?php
return [
    'doctrine' => [
        'connection' => [
            'odm_default' => [
                'server' => getenv('MONGO_SERVER'),
                'port' => getenv('MONGO_PORT')
            ]
        ],
        'configuration' => [
            'odm_default' => [
                'default_db' => 'valu_modeler_test'
            ]
        ]
    ],
];
