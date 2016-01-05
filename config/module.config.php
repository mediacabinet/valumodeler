<?php
return [
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'ValuModeler\Model' => 'valumodeler',
		            'ValuX' => 'valux'
                ]
            ],
            'valumodeler' => [
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'paths' => [
                    realpath(__DIR__ . '/../src/ValuModeler/Model')
                ]
            ],
	        'valux' => [
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'paths' => [
                    'data/valumodeler/documents/ValuX'
                ]
            ]
        ]
    ],
    'valu_modeler' => [
        'class_dir' => 'data/valumodeler/documents',
        'field_types' => [
            'string' => [
                'class' => 'ValuModeler\\FieldType\\String',
            ],
            'text' => [
                'class' => 'ValuModeler\\FieldType\\Text',
            ],
            'integer' => [
                'class' => 'ValuModeler\\FieldType\\Integer',
            ],
            'float' => [
                'class' => 'ValuModeler\\FieldType\\Float',
            ],
            'date' => [
                'class' => 'ValuModeler\\FieldType\\Date',
            ],
            'boolean' => [
                'class' => 'ValuModeler\\FieldType\\Boolean',
            ],
            'collection' => [
                'class' => 'ValuModeler\\FieldType\\Collection',
            ],
            'map' => [
                'class' => 'ValuModeler\\FieldType\\Map',
            ],
            'email' => [
                'class' => 'ValuModeler\\FieldType\\Email',
            ],
        ],
        'cache' => [
            'adapter' => [
                'name' => 'memory',
            ]
        ],
    ],
    'service_manager' => [
        'factories' => [
            'valu_modeler.metadata_injector' => 'ValuModeler\\ServiceManager\\MetadataInjectorFactory',
            'ValuModelerInputFilterDelegate' => 'ValuModeler\\ServiceManager\\InputFilterDelegateFactory',
        ],
    ],
    'valu_so' => [
        'abstract_factories' => [
            'ValuModeler\\Service\\ServiceFactory'
        ],
        'services' => [
            'ValuModelerDocument' => [
                'name' => 'Modeler.Document',
            ],
            'ValuModelerAssociation' => [
                'name' => 'Modeler.Association',
            ],
            'ValuModelerField' => [
                'name' => 'Modeler.Field',
            ],
            'ValuModelerImporter' => [
                'name' => 'Modeler.Importer',
                'class' => 'ValuModeler\\Service\\ImporterService',
            ],
            'ValuModelerSetup' => [
                'name' => 'ValuModeler.Setup',
                'class' => 'ValuModeler\\Service\\SetupService',
                'config' => 'vendor/valu/valumodeler/config/setup.config.php',
            ],
        ]
    ],
    'array_adapter' => [
        'model_listener' => [
            'namespaces' => [
                'ValuX' => 'ValuX\\',
                'ValuModeler' => 'ValuModeler\\'
            ]
        ]
    ],
    'input_filter' => [
        'config' => require __DIR__ . '/input-filter.config.php',
        'delegates' => [
            'ValuModelerInputFilterDelegate' => [
                'delegate' => 'ValuModelerInputFilterDelegate',
            ],
        ],
    ],
];
