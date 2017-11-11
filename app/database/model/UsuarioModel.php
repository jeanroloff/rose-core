<?php

namespace App\Model;

use Core\Data\Model;

class UsuarioModel extends Model
{
	protected $table = 'dia';

	protected $aliases = [
		'DIA' => 'date'
	];
}