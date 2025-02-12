<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomReportLog extends Model
{
    use HasFactory;

    protected $table = 'custom_report_logs';

    protected $fillable = [
        'type',
        'message',
        'custom_report_type_id',
        'custom_report_id',
        'user_id',
        'filepath',
        'template_filepath',
    ];
}
