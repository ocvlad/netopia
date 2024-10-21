<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
$loader->load('services.yaml');
$container->compile();

$routesClosure = require __DIR__ . '/../src/Routes/web.php';
$routes = $routesClosure($container);

$request = Request::createFromGlobals();
$context = new RequestContext();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());
    $controllerOrClosure = $parameters['_controller'];
    unset($parameters['_controller']);
    unset($parameters['_route']);

    if (is_callable($controllerOrClosure)) {
        $response = call_user_func_array($controllerOrClosure, [$request, ...array_values($parameters)]);
    } else {
        $controllerInstance = $container->get($controllerOrClosure[0]);
        $method = $controllerOrClosure[1];
        $response = call_user_func_array([$controllerInstance, $method], [$request, ...array_values($parameters)]);
    }

} catch (ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
} catch (Exception $e) {
    $response = new Response('An error occurred: ' . $e->getMessage(), 500);
}

$response->send();
