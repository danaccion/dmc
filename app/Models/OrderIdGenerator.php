<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderIdGenerator extends Model
{
    use HasFactory;
    protected $table = 'orderidgenerator';
    protected $fillable = ['client_id','status','invoice_no'];
}
