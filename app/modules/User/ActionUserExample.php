<?php

namespace App\Modules\User;

use Core\Action\Output;

class ActionUserExample extends Output
{
// ---------
//	Overriding this method we can add content to the "outputData" property, wich can be easily accessed from the Twig file.
// ---------
	public function setOutputData()
	{
		$this->outputData = [
			'name' => 'Rose',
			'version' => 'Testing'
		];
	}
}