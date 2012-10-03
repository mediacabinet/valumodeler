<?php
return [
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
        ],
    ],
    'service_manager' => [
        'factories' => [
            'ValuModelerDm' => 'ValuModeler\\ServiceManager\\DocumentManagerFactory',
            'ValuModelerMetadataInjector' => 'ValuModeler\\ServiceManager\\MetadataInjectorFactory',
            'ValuModelerInputFilterDelegate' => 'ValuModeler\\ServiceManager\\InputFilterDelegateFactory',
        ],
    ],
    'services' => [
        'ValuModelerDocument' => [
            'name' => 'Modeler.Document',
            'factory' => 'ValuModeler\\ServiceManager\\DocumentServiceFactory',
        ],
        'ValuModelerSetup' => [
            'name' => 'Modeler.Setup',
            'class' => 'ValuModeler\\Service\\Setup',
            'config' => 'module/ValuModeler/config/setup.config.php',
        ],
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
                    'required' => '',
                ],
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
                            'haystack' => [
                                'embed_one',
                                'embed_many',
                            ],
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
                            'haystack' => [
                                'reference_one',
                                'reference_many',
                            ],
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
