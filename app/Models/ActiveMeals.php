<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class ActiveMeals extends Model {
    use HasFactory;

    protected $table = 'meals';

    protected $connection = 'second_db';
}
