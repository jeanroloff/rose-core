<?php

namespace Core\Action;


class Insert extends Input
{

	public function execute()
	{
		$this->setFields();
		$this->save();
	}

	protected function onSuccess()
	{
		$this->status = "Success";
		$this->responseCode = 201;
	}

	protected function onFailure()
	{
		$this->status = "Error";
		$this->responseCode = 400;
	}
}