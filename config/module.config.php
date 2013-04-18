<?php
return [
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'ValuModeler\Model' => 'valumodeler'
                ]
            ],
            'valumodeler' => [
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'paths' => [
                    __DIR__ . '/../src/ValuModeler/Model'
                ]
            ]
        ]
    ],
    'valu_modeler' => [
        'class_dir' => 'data/valu-modeler/documents',
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
            'enabled' => true,
            'adapter' => [
                'name' => 'memory',
                'namespace' => md5(__DIR__).'_valu_modeler'
            ]
        ],
    ],
    'service_manager' => [
        'factories' => [
            'ValuModelerMetadataInjector' => 'ValuModeler\\ServiceManager\\MetadataInjectorFactory',
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
            'ValuModelerEmbed' => [
                'name' => 'Modeler.Embed',
            ],
            'ValuModelerField' => [
                'name' => 'Modeler.Field',
            ],
            'ValuModelerSetup' => [
                'name' => 'ValuModeler.Setup',
                'class' => 'ValuModeler\\Service\\SetupService',
                'config' => 'vendor/valu/valumodeler/config/setup.config.php',
            ],
        ]
    ],
    'input_filter' => [
        'config' => [
            'ValuModelerDocument' => [
                'type' => 'Valu\\InputFilter\\InputFilter',
                'name' => [
                    'required' => true,
                    'validators' => [
                        [
                            'name' => 'ValuModeler\\Validator\\DocumentName',
                        ],
                    ],
                ],
                'collection' => [
                    'required' => false,
                ]
            ],
            'ValuModelerField' => [
                'type' => 'Valu\\InputFilter\\InputFilter',
                'name' => [
                    'validators' => [
                        [
                            'name' => 'ValuModeler\\Validator\\FieldName',
                        ],
                    ],
                ],
                'fieldType' => [
                    'required' => true,
                ],
            ],
            'ValuModelerEmbed' => [
                'type' => 'Valu\\InputFilter\\InputFilter',
                'name' => [
                    'validators' => [
                        [
                            'name' => 'ValuModeler\\Validator\\FieldName',
                        ],
                    ],
                ],
                'embedType' => [
                    'validators' => [
                        [
                            'name' => 'inarray',
                            'options' => [
                                'haystack' => [
                                    'embed_one',
                                    'embed_many',
                                ],
                            ]
                            
                        ],
                    ],
                ],
                'embedDocument' => [
                    'validators' => [
                        [
                            'name' => 'ValuModeler\\Validator\\DocumentName',
                        ],
                    ],
                ],
            ],
            'ValuModelerReference' => [
                'type' => 'Valu\\InputFilter\\InputFilter',
                'name' => [
                    'validators' => [
                        [
                            'name' => 'ValuModeler\\Validator\\FieldName',
                        ],
                    ],
                ],
                'refType' => [
                    'validators' => [
                        [
                            'name' => 'inarray',
                            'options' => [
                                'haystack' => [
                                    'embed_one',
                                    'embed_many',
                                ],
                            ]
                        ],
                    ],
                ],
                'document' => [
                    'validators' => [
                        [
                            'name' => 'ValuModeler\\Validator\\DocumentName',
                        ],
                    ],
                ],
            ],
        ],
        'delegates' => [
            'ValuModelerInputFilterDelegate' => [
                'delegate' => 'ValuModelerInputFilterDelegate',
            ],
        ],
    ],
];
