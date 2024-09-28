<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispenseFeedback extends Model
{
    use HasFactory;

    protected $table = 'dispense_feedback';

    protected $guarded = [];
}
