<?php

namespace App\Services\FormService;

use App\Models\Formulario;
use App\Contracts\Services\FormService\FormServiceInterface;

class FormService implements FormServiceInterface
{
    public function isGaapForm($code)
    {
        $results = Formulario::where('codigo', $code)
            ->where('formulario.prefix', 'us-gaap')->get();

        return $results->count() > 0;
    }

    public function isIfrs($code)
    {
        $results = Formulario::where('codigo', $code)
            ->where('formulario.prefix', 'ifrs-full')->get();

        return $results->count() > 0;
    }

    public function formToJson($code)
    {
        $results = Formulario::raw(function($collection) use ($code) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'codigo' => [
                            '$eq' => $code
                        ]
                    ]
                ],
                [
                    '$project' => [
                        'fields' => [
                            '$filter' => [
                                'input' => '$formulario',
                                'as' => 'form',
                                'cond' => [
                                    '$or' => [
                                        ['$eq' => ['$$form.name', 'DocumentPeriodEndDate']],
                                        ['$or' =>
                                            [
                                                [
                                                    '$and' => [
                                                        ['$eq' => ['$$form.name', 'NetIncomeLoss']],
                                                        ['$eq' => ['$$form.prefix', 'us-gaap']]
                                                    ]
                                                ],
                                                [
                                                    '$and' => [
                                                        ['$or' => [
                                                                ['$eq' => ['$$form.name', 'ProfitLossAttributableToOwnersOfParent']],
                                                                ['$eq' => ['$$form.name', 'ProfitLoss']],
                                                            ]
                                                        ],
                                                        ['$eq' => ['$$form.prefix', 'ifrs-full']]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                        ],
                        'source' => '$fuente'
                    ]
                ]
            ]);
        });

        $endOfPeriod = current(array_filter( (array) $results[0]['fields'], function ($item) {
            return $item['name'] == 'DocumentPeriodEndDate';
        }));

        if ($endOfPeriod) {
            $endOfPeriod = $endOfPeriod['value'];
        }

        $netIncomeLoss = current(array_filter( (array) $results[0]['fields'], function ($item) use ($endOfPeriod) {
            return ($item['name'] == 'NetIncomeLoss' || $item['name'] == 'ProfitLoss' || $item['name'] == 'ProfitLossAttributableToOwnersOfParent')
                && $item['periods'][1]['period_value'] == $endOfPeriod;
        }));

        if ($netIncomeLoss) {
            $netIncomeLoss = $netIncomeLoss['value'];
        }

        return [
            'netIncomeLoss' => $netIncomeLoss,
            'endOfPeriod' => $endOfPeriod,
            'source' => $results['0']['source']
        ];

    }

    public function getForms($company)
    {
        return Formulario::raw(function($collection) use ($company) {
            return $collection->aggregate([
                [
                    '$match' => [
                        '$and' => [
                            [
                                'empresa_id' => [
                                    '$eq' => $company->id
                                ]
                            ],
                            [
                                '$or' => [
                                    [
                                        'tipo' => [
                                            '$eq' => '10-K'
                                        ]
                                    ],
                                    [
                                        'tipo' => [
                                            '$eq' => '20-F'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                [
                    '$project' => [
                        'tipo' => '$tipo',
                        'codigo' => '$codigo',
                        'empresa_id' => '$empresa_id'
                    ]
                ]
            ]);
        });
    }
}
