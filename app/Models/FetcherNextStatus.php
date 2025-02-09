<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FetcherNextStatus extends Model
{
    use HasFactory;

    protected $table = 'fetchers_next_status';
    protected $guarded = [];
}
