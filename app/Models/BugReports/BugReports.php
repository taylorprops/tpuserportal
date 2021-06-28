<?php

namespace App\Models\BugReports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugReports extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'bug_reports';
    protected $guarded = [];

}
