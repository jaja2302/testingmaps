<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlotAfdelingGis extends Model
{
    use HasFactory;
    protected $table = 'afdeling_plot_gis';
    protected $guarded = ['id'];
    public $timestamps = false;
}
