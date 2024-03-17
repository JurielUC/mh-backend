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

        'bill_no',

        'name',
        'description',

        'price',
        'date',
        'from_date',
        'to_date',
        'due',

        'deprecated',
    ];

    public function generate_code()
	{
		$code = "MH-".mt_rand(100, 999).time();

		return $code;
	}

    public function gen_bill_no()
	{
        $bill_no = "";

		$where = [
			['deprecated', '=', 0],
		];
		$bill = Bill::where($where)->orderBy('id', 'desc')->first();

		if ($bill) {
			$bill_no = $bill->bill_no + 1;
		} else {
			$bill_no = 100;
		}

		return $bill_no;
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
