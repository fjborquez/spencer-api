<?php

namespace App\Contracts\Services\FormService;

interface FormServiceInterface
{
    public function isGaapForm($form);

    public function isIfrs($form);

    public function formToJson($code);

    public function getForms($company);
}
