<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class Formulario extends Model
{
    use HasFactory;
    protected $table = 'formularios';
    protected $fillable = ['codigo', 'tipo', 'empresa_id', 'formulario'];
    protected $connection = 'mongodb';

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
