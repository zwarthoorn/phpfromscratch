<?php

namespace routing;

use http\Client\Response;
use PhpOpdracht\Repository\UserReposetory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class routing
{
    const SPACE = 'PhpOpdracht\Controller';
    public function routing()
    {
        $routes = $this->createRoutes();
        try
        {

            $context = new RequestContext();
            $context->fromRequest(Request::createFromGlobals());

            $matcher = new UrlMatcher($routes, $context);

            $parameters = $matcher->match($context->getPathInfo());

            list($id, $toUse) = $this->createControllerLink($parameters);
            $request = Request::createFromGlobals();

            $repo = new UserReposetory();
            $user = $repo->find('id', (int) $request->cookies->get('userId'));

            $request->request->set('user',$user);

            return $toUse($request,$id);

        }
        catch (ResourceNotFoundException $e)
        {
            return  $e->getMessage();
        }
    }


    protected function createRoutes()
    {
// Init basic route
        $route[] = [
            'route' => new Route(
                '/',
                array('controller' => 'HomeController', 'method' => 'index')
            ),
            'name' => 'index'
        ];

        $route[] = [
            'route' => new Route(
                '/register',
                array('controller' => 'RegisterController', 'method' => 'index')
            ),
            'name' => 'register'
        ];

        $route[] = [
            'route' => new Route(
                '/newregister',
                array('controller' => 'RegisterController', 'method' => 'newRegister')
            ),
            'name' => 'newRegister'
        ];

        $route[] = [
            'route' => new Route(
                '/edit/{id}',
                array('controller' => 'UserController', 'method' => 'edit')
            ),
            'name' => 'edit'
        ];

        $route[] = [
            'route' => new Route(
                '/save/{id}',
                array('controller' => 'UserController', 'method' => 'save')
            ),
            'name' => 'editsave'
        ];

        $route[] = [
            'route' => new Route(
                '/list',
                array('controller' => 'UserController', 'method' => 'userList')
            ),
            'name' => 'list'
        ];

        $route[] = [
            'route' => new Route(
                '/makeadmin/{id}',
                array('controller' => 'UserController', 'method' => 'makeAdmin')
            ),
            'name' => 'makeadmin'
        ];
        $route[] = [
            'route' => new Route(
                '/delete/{id}',
                array('controller' => 'UserController', 'method' => 'delete')
            ),
            'name' => 'delete'
        ];

        $route[] = [
            'route' => new Route(
                '/logout',
                array('controller' => 'UserController', 'method' => 'logout')
            ),
            'name' => 'delete'
        ];

        $route[] = [
            'route' => new Route(
                '/login',
                array('controller' => 'HomeController', 'method' => 'login')
            ),
            'name' => 'login'
        ];

        $route[] = [
            'route' => new Route(
                '/vergeten',
                array('controller' => 'HomeController', 'method' => 'forgot')
            ),
            'name' => 'forgot'
        ];

        $route[] = [
            'route' => new Route(
                '/getnewpassword',
                array('controller' => 'HomeController', 'method' => 'getNewPassword')
            ),
            'name' => 'getnew'
        ];
        return $this->linkRoutes($route);
    }

    /**
     * @return RouteCollection
     */
    protected function linkRoutes($route)
    {
        $routes = new RouteCollection();

        foreach ($route as $router)
        {
            $routes->add($router['name'], $router['route']);
        }

        return $routes;
    }

    /**
     * @param array $parameters
     * @return array
     */
    protected function createControllerLink(array $parameters)
    {
        $controller = $parameters['controller'];
        $method = $parameters['method'];
        $id = $parameters['id'];
        $toUse = sprintf('%s\%s::%s', self::SPACE, $controller, $method);

        return array($id, $toUse);
    }
}