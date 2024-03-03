<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostFound extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'type',
        'code',

        'item_name',
        'location',
        'date_time',
        'finder_name',

        'image_urls',

        'deprecated',
    ];

    public function generate_code()
	{
		$code = "MH-".mt_rand(100, 999).time();

		return $code;
	}

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
