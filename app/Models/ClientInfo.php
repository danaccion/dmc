<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'pay_no',
        'name',
        'client_currency',
        'country',
        'created_at'
    ];
}
