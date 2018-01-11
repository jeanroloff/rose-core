<?php

namespace core\System;

interface INotification
{
	public function __construct($data);

	public function send() : bool;

	public function onSuccess();

	public function onError();
}