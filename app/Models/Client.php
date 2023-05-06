<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'gkojgnvu_client';
    protected $fillable = ['id','name', 'pay_no','use_no','role','client_currency','status','country'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function oGenerator()
    {
        return $this->hasOne(OrderIdGenerator::class,'client_id', 'id')->latestOfMany();
    }
    
    public function client_info()
    {
        return $this->belongsTo(ClientInfo::class,'id','client_id');
    }
}
