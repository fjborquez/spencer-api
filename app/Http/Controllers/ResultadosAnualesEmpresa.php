<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Formulario;
use App\Contracts\Services\FormService\FormServiceInterface;
use Illuminate\Support\Facades\App;

class ResultadosAnualesEmpresa extends Controller
{
    public function get($cik) {
        $formService = App::make(FormServiceInterface::class);

        $company = Empresa::where('cik', $cik)->first();
        $forms = $formService->getForms($company);
        $formsInJson = [];

        foreach($forms as $form) {
            $code = $form->codigo;

            if ($formService->isGaapForm($code)) {
                $formsInJson[] = $formService->gaapFormToJson($form);
            } elseif ($formService->isIfrs($code)) {
                $formsInJson[] = $formService->ifrsFormToJson($form);
            }
        }

        return $formsInJson;
    }
}
