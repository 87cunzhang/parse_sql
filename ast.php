<?php
/**
 * Created by 村长
 * Date: 2020-09-10
 * Time: 17:56
 */
//        [
//            'kind' => 'top',
//            'attr' => '',
//            'child' => [
//                // fields
//                [
//                    'kind' => 'fields',
//                    'attr' => '',
//                    'child' => [
//                        [
//                            'kind' => '*',
//                            'attr' => '*'
//                        ],
//                        [
//                            'kind' => 'id',
//                            'attr' => 'b'
//                        ]
//                    ]
//                ],
//                // table
//                [
//                    'kind' => 'id',
//                    'attr' => 't'
//                ],
//                // where clause
//                [
//                    'kind' => 'where',
//                    'attr' => '',
//                    'child' => [
//                        [
//                            'kind' => 'infixExpr',
//                            'attr' => '=',
//                            'child' => [
//                                [
//                                    'kind' => 'id',
//                                    'attr' => 'a'
//                                ],
//                                // 1+2/(3-4)
//                                [
//                                    'kind' =>  'infixExpr',
//                                    'attr' => '+',
//                                    'child' => [
//                                        [
//                                            'kind' => 'number',
//                                            'attr' => 1
//                                        ],
//                                        [
//                                            'kind' => 'infixExpr',
//                                            'attr' => '/',
//                                            'child' => [
//                                                [
//                                                    'kind' => 'number',
//                                                    'attr' => 2
//                                                ],
//                                                [
//                                                    'kind' => 'infixExpr',
//                                                    'attr' => '-',
//                                                    'child' => [
//                                                        [
//                                                            'kind' => 'number',
//                                                            'attr' => 3
//                                                        ],
//                                                        [
//                                                            'kind' => 'number',
//                                                            'attr' => 4
//                                                        ]
//                                                    ]
//                                                ]
//                                            ]
//                                        ]
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ]
//                ],
//                // order by
//                [
//                    'kind' => 'orderBy',
//                    'attr' => '',
//                    'child' => [
//                        [
//                            'kind' => 'orderByGroup',
//                            'attr' => 'asc',
//                            'child' => [
//                                [
//                                    'kind' => 'id',
//                                    'attr' => 'id'
//                                ]
//                            ]
//                        ],
//                        [
//                            'kind' => 'orderByGroup',
//                            'attr' => 'desc',
//                            'child' => [
//                                [
//                                    'kind' => 'id',
//                                    'attr' => 'time'
//                                ]
//                            ]
//                        ]
//                    ]
//                ],
//                // limit
//                [
//                    'kind' => 'limit',
//                    'attr' => '',
//                    'child' => [
//                        [
//                            'kind' => 'number',
//                            'attr' => 10
//                        ],
//                        [
//                            'kind' => 'number',
//                            'attr' => 0
//                        ]
//                    ]
//                ]
//            ]
//        ]
//    ]