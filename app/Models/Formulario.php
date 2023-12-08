<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Mongodb\Eloquent\Model;

class Formulario extends Model
{
    use HasFactory;
    protected $table = 'formularios';
    protected $fillable = ['codigo', 'tipo', 'empresa_id', 'formulario'];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
