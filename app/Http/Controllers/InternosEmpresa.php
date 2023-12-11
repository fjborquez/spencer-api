<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Formulario;

class InternosEmpresa extends Controller
{
    public function get($cik){
        $empresa = Empresa::where('cik', $cik)->first();
        return Formulario::raw(function ($collection) use ($empresa){
            return $collection->aggregate([
                [
                    '$match' => [
                        'empresa_id' => ['$eq' => $empresa->id],
                        'tipo' => ['$eq' => '4'],
                    ],
                ],
                [
                    '$addFields' => [
                        'formulario.position' => [
                            '$cond' => [
                                'if' => [
                                    '$and' => [
                                        '$formulario.reportingOwner.reportingOwnerRelationship.officerTitle',
                                        [
                                            '$not' => [
                                                '$eq' => [
                                                    '$formulario.reportingOwner.reportingOwnerRelationship.officerTitle',
                                                    new \stdClass()
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'then' => '$formulario.reportingOwner.reportingOwnerRelationship.officerTitle',
                                'else' => [
                                    '$cond' => [
                                        'if' => '$formulario.reportingOwner.reportingOwnerRelationship.isDirector',
                                        'then' => 'Director',
                                        'else' => [
                                            '$cond' => [
                                                'if' => '$formulario.reportingOwner.reportingOwnerRelationship.isTenPercentOwner',
                                                'then' => '10% Owner',
                                                'else' => '',
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$group' => [
                        '_id' => '$formulario.reportingOwner.reportingOwnerId.rptOwnerCik',
                        'name' => [
                            '$first' => '$formulario.reportingOwner.reportingOwnerId.rptOwnerName'
                        ],
                        'position' => [
                            '$first' => '$formulario.position'
                        ],
                        'shares' => [
                            '$first' => '$formulario.nonDerivativeTable.nonDerivativeTransaction.postTransactionAmounts.sharesOwnedFollowingTransaction.value',
                        ],
                        'updatedAt' => [
                            '$first' => '$formulario.periodOfReport'
                        ],
                    ]
                ],
                [
                    '$addFields' => [
                        'shares' => [
                            '$cond' => [
                                'if' => ['$isArray' => '$shares'],
                                'then' => ['$last' => '$shares'],
                                'else' => [
                                    '$cond' => [
                                        'if' => '$shares',
                                        'then' => '$shares',
                                        'else' => '0'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$sort' => ['updatedAt' => -1]
                ]
            ]);
        });
    }
}
