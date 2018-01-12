<?php

namespace Core\System;

interface INotification
{
	public function send() : bool;

	public function onSuccess();

	public function onError();
}