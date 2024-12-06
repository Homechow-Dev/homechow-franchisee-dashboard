<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class HomechowAccounts extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'homechow_accounts';
    
    protected $fillable = [
        'id',
        'user_id',
        'Name',
        'Email',
        'Phone',
        'WalletAmount',
        'City',
        'State',
        'Zip',
        'Status',
        'Type',
        'PresentAddress',
        'PermanentAddress',
        'Gender',
        'Status',
        'FirstPass',
        'DateofBirth',
        'StripeAccountID',
        'ImageUrl',
    ];

}
