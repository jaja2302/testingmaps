<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlotCompanyGis extends Model
{
    use HasFactory;
    protected $table = 'company_plot_gis';
    protected $guarded = ['id'];
    public $timestamps = false;
}
