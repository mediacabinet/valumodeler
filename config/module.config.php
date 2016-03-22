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
                'class' => 'ValuModeler\\FieldType\\StringField',
            ],
            'text' => [
                'class' => 'ValuModeler\\FieldType\\TextField',
            ],
            'integer' => [
                'class' => 'ValuModeler\\FieldType\\IntegerField',
            ],
            'float' => [
                'class' => 'ValuModeler\\FieldType\\FloatField',
            ],
            'date' => [
                'class' => 'ValuModeler\\FieldType\\DateField',
            ],
            'boolean' => [
                'class' => 'ValuModeler\\FieldType\\BooleanField',
            ],
            'collection' => [
                'class' => 'ValuModeler\\FieldType\\CollectionField',
            ],
            'map' => [
                'class' => 'ValuModeler\\FieldType\\MapField',
            ],
            'email' => [
                'class' => 'ValuModeler\\FieldType\\EmailField',
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
