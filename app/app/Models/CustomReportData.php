<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomReportData extends Model
{
    use HasFactory;

    protected $table = 'custom_report_datas';

    protected $fillable = [
        "departament_id",
        "user_id",
        "customreporttype_id",
        "page",
        "row",
        "column",
        "value",
        "type",
        "loaded_at",
    ];
}
