<?php

namespace App\Routing;

use App\Controller\ProductController;
use App\Routing\Attribute\Route as RouteAttribute;
use App\Routing\Exception\RouteNotFoundException;
use App\Utils\Filesystem;
use Psr\Container\ContainerInterface;

class Router
{
    /** @var Route[] */
    private array $routes = [];

    private const CONTROLLERS_BASE_DIR = __DIR__ . "/../Controller/";
    private const CONTROLLERS_NAMESPACE_PREFIX = "App\\Controller\\";

    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function registerRoutes(): void
    {
        // Explorer le répertoire des contrôleurs
        // Construire tous les noms de classes (FQCN)
        // IndexController.php => IndexController => App\Controller\IndexController
        $controllersFqcn = Filesystem::getFqcns(self::CONTROLLERS_BASE_DIR, self::CONTROLLERS_NAMESPACE_PREFIX);

        foreach ($controllersFqcn as $fqcn) {
            $classInfos = new \ReflectionClass($fqcn);

            if ($classInfos->isAbstract()) {
                continue;
            }

            /** @var \ReflectionMethod[] */
            $methods = $classInfos->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if ($method->isConstructor()) {
                    continue;
                }

                $attributes = $method->getAttributes(RouteAttribute::class);

                if (!empty($attributes)) {
                    /** @var \ReflectionAttribute */
                    $routeAttribute = $attributes[0];
                    /** @var RouteAttribute */
                    $route = $routeAttribute->newInstance();
                    $this->addRoute(new Route(
                        $route->getUri(),
                        $route->getName(),
                        $route->getHttpMethod(),
                        $fqcn,
                        $method->getName()
                    ));
                }
            }
        }
    }

    public function addRoute(Route $route): self
    {
        $ajout = true;
        foreach ($this->routes as $r) {
            if($r == $route) {
                $ajout = false;
            }
        }

        if($ajout === true) {
            $this->routes[] = $route;
        }
        return $this;
    }

    /**
     * Executes a route against given URI and HTTP method
     *
     * @param string $uri
     * @param string $httpMethod
     * @return void
     * @throws RouteNotFoundException
     */
    public function execute(string $uri, string $httpMethod): string
    {
        $route = $this->getRoute($uri, $httpMethod);

        if ($route === null) {
            throw new RouteNotFoundException();
        }

        // Constructeur
        $controllerClass = $route->getControllerClass();
        $constructorParams = $this->getMethodParams($controllerClass . '::__construct');
        $controllerInstance = new $controllerClass(...$constructorParams);


        // Contrôleur
        $method = $route->getController();

        // si le contrôleur est "item()", on récupère l'id contenu dans l'url pour le passer dans ses paramètres
        if($method == "item") {
            $tabUri = explode('/', $uri);
            $idProductUri = intval(end($tabUri));
        }

        $controllerParams = $this->getMethodParams($controllerClass . '::' . $method);
        if(isset($idProductUri)) {
            return $controllerInstance->$method($idProductUri, ...$controllerParams);
        } else {
            return $controllerInstance->$method(...$controllerParams);
        }
    }

    public function getRoute(string $uri, string $httpMethod): ?Route
    {
        $regExpr = "/\{(\w+)\}/";
        $regExpr2 = "#/products/(\d+)#";

        foreach ($this->routes as $savedRoute) {
            // on vérifie si l'url de la requête est au format "/products/{id}", avec {id} qui vaut un nombre entier
            if(preg_match($regExpr, $savedRoute->getUri()) && preg_match($regExpr2, $uri)) {
                return $savedRoute;
            }

            if ($savedRoute->getUri() === $uri && $savedRoute->getHttpMethod() === $httpMethod) {
                return $savedRoute;
            }
        }
        return null;
    }

    private function getMethodParams(string $method): array
    {

        $methodInfos = new \ReflectionMethod($method);
        // $methodInfos = new \ReflectionMethod("App\Controller\ProductController::item");
        $methodParameters = $methodInfos->getParameters();

        $params = [];
        foreach ($methodParameters as $param) {
            $paramType = $param->getType();
            $paramTypeFQCN = $paramType->getName();

            // on identifie le moment où on examine le paramètre "idProduct" de la méthode "App\Controller\ProductController::item"
            if($method === "App\Controller\ProductController::item" && $paramTypeFQCN === "int") {
                continue;
            } else {
                $params[] = $this->container->get($paramTypeFQCN);
            }
        }

        return $params;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
