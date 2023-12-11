<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;
    protected $table = 'empresas';
    protected $fillable = ['nombre', 'ticker', 'exchange', 'cik'];
    protected $connection = 'mongodb';

    public function formularios(): HasMany
    {
        return $this->hasMany(Formulario::class);
    }
}
