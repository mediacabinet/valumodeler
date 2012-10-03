<?php
return [
    'documents' => [
        'address' => [
            'name' => 'Common\\UniversalAddress',
            'fields' => [
                'name1' => [
                    'type' => 'text',
                    'multiline' => '',
                ],
                'name2' => [
                    'type' => 'string',
                    'multiline' => '',
                ],
                'street1' => [
                    'type' => 'text',
                    'multiline' => '',
                ],
                'street2' => [
                    'type' => 'string',
                    'multiline' => '',
                ],
                'postalCode' => [
                    'type' => 'text',
                    'multiline' => '',
                    'required' => '',
                    'validators' => [
                        0 => [
                            'name' => 'stringlength',
                            'options' => [
                                'min' => '3',
                                'max' => '10',
                            ],
                        ],
                    ],
                ],
                'district' => [
                    'type' => 'text',
                    'multiline' => '',
                ],
                'city' => [
                    'type' => 'text',
                    'multiline' => '',
                ],
                'country' => [
                    'type' => 'text',
                    'multiline' => '',
                    'filters' => [
                        0 => [
                            'name' => 'stringtolower',
                        ],
                    ],
                    'validators' => [
                        0 => [
                            'name' => 'stringlength',
                            'options' => [
                                'min' => '3',
                                'max' => '3',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
