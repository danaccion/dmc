<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class ClientInfo extends Model
{
    use HasFactory;
    protected $table = 'gkojgnvu_client_info';
    protected $fillable = [
        'client_id',
        'post_id',
        'description',
        'invoice_no',
        'file',
        'currency',
        'orig_amount',
        'amount',
        'status',
        'created_at',
        'updated_at',
        'transaction_id',
        'additional_fee'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function orderIdGenerator()
    {
        return $this->hasMany(OrderIdGenerator::class);
    }
}