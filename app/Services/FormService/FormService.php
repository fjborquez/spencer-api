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

    public function gaapFormToJson($form)
    {
        $results = array_filter($form->formulario, function($f) {
            return $f['name'] == 'NetIncomeLoss' || $f['name'] == 'DocumentPeriodEndDate';
        });

        $endOfPeriod = current(array_filter($results, function($f) {
            return $f['name'] == 'DocumentPeriodEndDate';
        }));

        if ($endOfPeriod) {
            $endOfPeriod = $endOfPeriod['value'];
        }

        $netIncomeLoss = current(array_filter($results, function($f) use ($endOfPeriod) {
            return $f['name'] == 'NetIncomeLoss' && $f['periods'][1]['period_value'] == $endOfPeriod;
        }));

        if ($netIncomeLoss) {
            $netIncomeLoss = $netIncomeLoss['value'];
        }

        dd($netIncomeLoss);

        return [
            'NetIncomeLoss' => floatval($netIncomeLoss),
            'endOfPeriod' => $endOfPeriod
        ];
    }

    public function ifrsFormToJson($form)
    {
        $results = array_filter($form->formulario, function($f) {
            return $f['name'] == 'ProfitLoss' || $f['name'] == 'DocumentPeriodEndDate';
        });

        $endOfPeriod = current(array_filter($results, function($f) {
            return $f['name'] == 'DocumentPeriodEndDate';
        }));

        if ($endOfPeriod) {
            $endOfPeriod = $endOfPeriod['value'];
        }

        $netProfitLoss = current(array_filter($results, function($f) use ($endOfPeriod) {
            return $f['name'] == 'ProfitLoss' && $f['periods'][1]['period_value'] == $endOfPeriod;
        }));

        if ($netProfitLoss) {
            $netProfitLoss = $netProfitLoss['value'];
        }

        dd($netProfitLoss);

        return [
            'NetIncomeLoss' => floatval($netProfitLoss),
            'endOfPeriod' => $endOfPeriod
        ];
    }

    public function getForms($company)
    {
        return Formulario::where('empresa_id', $company->id)
            ->where(function ($query) {
                $query->where('tipo', '10-K')
                    ->orWhere('tipo', '20-F');
            })->get();
    }
}
