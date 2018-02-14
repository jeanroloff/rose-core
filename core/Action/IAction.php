<?php

namespace Core\Action;


use Core\Controller\IController;
use Slim\Http\Response;

interface IAction
{
	public function getMessageCannotExecute() : string;

	public function __construct(IController $controller, array $args);

	public function canExecute(array $args) : bool;

	public function validate();

	public function execute();

	public function setOutputData();

	public function getOutput() : Response;

	public function getResponseCode() : int;

}