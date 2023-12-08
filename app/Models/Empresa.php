<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Jenssegers\Mongodb\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;
    protected $table = 'empresas';
    protected $fillable = ['nombre', 'ticker', 'exchange', 'cik'];

    public function formularios(): HasMany
    {
        return $this->hasMany(Formulario::class);
    }
}
