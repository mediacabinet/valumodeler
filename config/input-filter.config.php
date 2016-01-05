<?php
return [
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
        ],
        'parent' => [
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
                    'name' => 'ValuModeler\\Validator\\ValidatorChain',
                ],
            ],
        ],
        'filters' => [
            'required' => false,
            'validators' => [
                [
                    'name' => 'ValuModeler\\Validator\\FilterChain',
                ],
            ],
        ]
    ],
    'ValuModelerAssociation' => [
        'type' => 'Valu\\InputFilter\\InputFilter',
        'name' => [
            'validators' => [
                [
                    'name' => 'ValuModeler\\Validator\\FieldName',
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
                    'name' => 'ValuModeler\\Validator\\DocumentName',
                ],
            ],
        ]
    ]
];
