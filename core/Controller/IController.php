<?php

namespace Core\Controller;


use Core\Data\Model;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

interface IController
{
	public function __construct(array $config, Container $container);

	public function __invoke(Request $request, Response $response, array $args) : Response;

	public function getModuleName() : string;

	public function getModel() : ?Model;

	public function getContainer() : Container;

	public function getResponse() : Response;

	public function getRoute() : array;
}