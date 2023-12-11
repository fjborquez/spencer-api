<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Formulario;

class ResultadosAnualesEmpresa extends Controller
{
    public function get($cik) {
        $empresa = Empresa::where('cik', $cik)->first();
        return Formulario::raw(function ($collection) use ($empresa){
            return $collection->aggregate([
                [
                    '$match' => [
                        'empresa_id' => ['$eq' => $empresa->id],
                        'tipo' => ['$eq' => '10-K'],
                    ],
                ],
                [
                    '$project' => [
                        'list' => [
                            '$filter' => [
                                'input' => '$formulario',
                                'as' => 'form',
                                'cond' => [
                                    '$and' => [
                                        ['$eq' => ['$$form.name', "NetIncomeLoss"]],
                                        ['$eq' => [['$size' => '$$form.dimensions'], 0]]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$sort' => [
                        'list.context_ref' => -1,
                    ]
                ],
                [
                    '$project' => [
                        'list' => [
                            '$sortArray' => [
                                'input' => '$list',
                                'sortBy' => [
                                    'context_ref' => -1
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$project' => [
                        'resultado' => [
                            '$first' => '$list'
                        ]
                    ]
                ]
            ]);
        });
    }
}
