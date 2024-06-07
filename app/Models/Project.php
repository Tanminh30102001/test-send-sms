<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'merchant_id',
        'name',
        'merchant_no',
        'project_secret'
    ];
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function logsends()
    {
        return $this->hasMany(Logsend::class);
    }
}
