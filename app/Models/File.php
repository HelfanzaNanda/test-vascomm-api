<?php

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\CreatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes, Blameable, CreatedBy;

    protected $guarded = [];
}
