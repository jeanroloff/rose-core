<?php

namespace App\Model;

use Core\Data\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserModel extends Model
{
	use SoftDeletes;

	protected $table = 'user';

	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = md5($value);
	}
}