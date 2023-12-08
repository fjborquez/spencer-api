<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;

class TransaccionesInternas extends Controller
{
    public function list() {
        return Formulario::all();
    }

    public function get($codigo) {
        return Formulario::where('codigo', $codigo)->first();
    }
}
