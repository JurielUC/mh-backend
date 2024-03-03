<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'type',
        'code',

        'name',
        'description',

        'deprecated',
    ];

    public function generate_code()
	{
		$code = "MH-".mt_rand(100, 999).time();

		return $code;
	}

    public function reservations()
    {
        return $this->hasMany('App\Models\Reservation');
    }
}
