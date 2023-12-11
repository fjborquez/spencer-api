<?php

namespace App\Http\Controllers;

use App\Models\Formulario;

class TransaccionesInternas extends Controller
{
    public function list() {
        return Formulario::all();
    }

    public function get($codigo) {
        return Formulario::where('codigo', $codigo)->first();
    }
}
