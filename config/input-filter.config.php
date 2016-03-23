<?php
return [
    'ValuModelerDocument' => [
        'type' => 'Valu\InputFilter\InputFilter',
        'name' => [
            'required' => true,
            'validators' => [
                [
                    'name' => 'ValuModeler\Validator\DocumentNameValidator',
                ],
            ],
        ],
        'collection' => [
            'required' => false,
        ],
        'parent' => [
            'required' => false,
        ]
    ],
    'ValuModelerField' => [
        'type' => 'Valu\InputFilter\InputFilter',
        'name' => [
            'validators' => [
                [
                    'name' => 'ValuModeler\Validator\FieldNameValidator',
                ],
            ],
        ],
        'fieldType' => [
            'required' => true,
        ],
        'required' => [
            'required' => false,
            'filters' => [
                ['name' => 'boolean']
            ],
            'validators' => [
                [
                    'name' => 'not_empty',
                    'options' => ['type' => 'string'] // do not allow empty string
                ]
            ]
        ],
        'allowEmpty' => [
            'required' => false,
            'filters' => [
                ['name' => 'boolean']
            ],
            'validators' => [
                [
                    'name' => 'not_empty',
                    'options' => ['type' => 'string'] // do not allow empty string
                ]
            ]
        ],
        'validators' => [
            'required' => false,
            'validators' => [
                [
                    'name' => 'ValuModeler\Validator\ValidatorChainValidator',
                ],
            ],
        ],
        'filters' => [
            'required' => false,
            'validators' => [
                [
                    'name' => 'ValuModeler\Validator\FilterChainValidator',
                ],
            ],
        ]
    ],
    'ValuModelerAssociation' => [
        'type' => 'Valu\\InputFilter\\InputFilter',
        'name' => [
            'validators' => [
                [
                    'name' => 'ValuModeler\Validator\FieldNameValidator',
                ],
            ],
        ],
        'associationType' => [
            'validators' => [
                [
                    'name' => 'inarray',
                    'options' => [
                        'haystack' => [
                            'reference_one',
                            'reference_many',
                        ],
                    ]

                ],
            ],
        ],
        'refDocument' => [
            'validators' => [
                [
                    'name' => 'ValuModeler\Validator\DocumentNameValidator',
                ],
            ],
        ]
    ]
];
