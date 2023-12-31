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
            $formsInJson[] = $formService->formToJson($code);
        }

        usort($formsInJson, function ($current, $next) {
            if ($current['endOfPeriod'] === $next['endOfPeriod']) {
                return 0;
            }

            return $current['endOfPeriod'] < $next['endOfPeriod'] ? -1 : 1;
        });

        return $formsInJson;
    }
}
