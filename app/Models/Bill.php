<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'type',
        'code',

        'name',
        'description',

        'price',
        'date',
        'due',

        'deprecated',
    ];

    public function generate_code()
	{
		$code = "MH-".mt_rand(100, 999).time();

		return $code;
	}

    public function admin()
    {
        return $this->belongsTo('App\Models\User', 'admin_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
