<?php
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuthService;
use Symfony\Component\DependencyInjection\ContainerInterface;

function authenticatedRoute(array $controllerAndMethod, AuthService $authService)
{
    return function (Request $request, $id = null) use ($controllerAndMethod, $authService) {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return new Response("Unauthorized", 401);
        }

        $token = $matches[1];
        $payload = $authService->validateToken($token);

        if (!$payload) {
            return new Response("Invalid token", 401);
        }
        $request->attributes->set('user', $payload);

        return call_user_func_array([$controllerAndMethod[0], $controllerAndMethod[1]], [$request, $id]);
    };
}

return function (ContainerInterface $container) {
    $routes = new RouteCollection();
    $authService = $container->get(AuthService::class);
    $authController = $container->get(App\Controllers\AuthController::class);
    $departmentController = $container->get(App\Controllers\DepartmentController::class);

    $routes->add('login', new Route('/login', [
        '_controller' => [$authController, 'login']
    ], [], [], '', [], ['POST']));

    $routes->add('create_department', new Route('/departments', [
        '_controller' => authenticatedRoute([$departmentController, 'create'], $authService)
    ], [], [], '', [], ['POST']));

    $routes->add('get_department', new Route('/departments/{id}', [
        '_controller' => authenticatedRoute([$departmentController, 'get'], $authService)
    ], [], [], '', [], ['GET']));

    $routes->add('update_department', new Route('/departments/{id}', [
        '_controller' => authenticatedRoute([$departmentController, 'update'], $authService)
    ], [], [], '', [], ['PUT']));

    $routes->add('delete_department', new Route('/departments/{id}', [
        '_controller' => authenticatedRoute([$departmentController, 'delete'], $authService)
    ], [], [], '', [], ['DELETE']));

    $routes->add('get_descendants_by_name', new Route('/departments/descendants-by-name', [
        '_controller' => authenticatedRoute([$departmentController, 'getDescendantsByName'], $authService)
    ], [], [], '', [], ['POST']));

    return $routes;
};
