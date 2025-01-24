<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlosEstataGis extends Model
{
    use HasFactory;

    protected $table = 'estate_plot_gis';
    public $timestamps = false;

    protected $fillable = ['est', 'lat', 'lon', 'pt'];
}
