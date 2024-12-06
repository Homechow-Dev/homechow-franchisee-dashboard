<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kiosk extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'kiosks';

    protected $fillable = [
        'Account_id',
        'MachineID',
        'TradeNO',
        'KioskType',
        'KioskNumber',
        'KioskAddress',
        'City',
        'State',
        'Zip',
        'TotalSold',
        'Status',
        'DecimalLocation',
        'Latitude',
        'Longitude',
        'MealsSold',
        'Earnings',
    ];

    public function accounts(): BelongsTo {
        return $this->belongsTo(Account::class);
    }
    
    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }

    public function meals(): BelongsToMany {
        return $this->belongsToMany(Meal::class)->withPivot('Total', 'StockTotal', 'TotalSold', 'SlotNo');
    }

    public function restocks(): HasMany {
        return $this->hasMany(Restock::class);
    }

    public function discounts(): HasMany {
        return $this->hasMany(Discount::class);
    }

    public function mifi(): HasOne {
        return $this->hasOne(Mifi::class, 'kiosk_id');
    }
}
