<?php

namespace App\Contracts\Services\FormService;

interface FormServiceInterface
{
    public function isGaapForm($form);

    public function isIfrs($form);

    public function gaapFormToJson($form);

    public function ifrsFormToJson($form);

    public function getForms($company);
}
