<?php

namespace App\Providers;

use App\Contracts\Services\FormService\FormServiceInterface;
use App\Services\FormService\FormService;
use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->bind(FormServiceInterface::class, FormService::class);
    }
}
