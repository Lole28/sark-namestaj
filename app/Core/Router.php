<?php

/**
 * Minimalni HTTP ruter.
 * Podržava parametre u putanji ( {id}, {slug} ) i "method override"
 * preko skrivenog polja _method za PATCH/DELETE iz HTML formi.
 */
class Router
{
    /** @var array<int,array{method:string,regex:string,handler:string}> */
    private array $routes = [];

    public function get(string $path, string $handler): void    { $this->add('GET', $path, $handler); }
    public function post(string $path, string $handler): void   { $this->add('POST', $path, $handler); }
    public function patch(string $path, string $handler): void  { $this->add('PATCH', $path, $handler); }
    public function delete(string $path, string $handler): void { $this->add('DELETE', $path, $handler); }

    private function add(string $method, string $path, string $handler): void
    {
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $path);
        $this->routes[] = [
            'method'  => $method,
            'regex'   => '#^' . $regex . '$#',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        if ($method === 'POST' && !empty($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $path, $m)) {
                $params = [];
                foreach ($m as $key => $value) {
                    if (!is_int($key)) {
                        $params[] = $value;
                    }
                }
                $this->invoke($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        $c = new Controller();
        $c->prikazi('greske/404', ['naslov' => 'Stranica nije pronađena']);
    }

    private function invoke(string $handler, array $params): void
    {
        [$class, $action] = explode('@', $handler);
        if (!class_exists($class) || !method_exists($class, $action)) {
            http_response_code(500);
            echo "Ruta pokazuje na nepostojeći kontroler: {$handler}";
            return;
        }
        $controller = new $class();
        $controller->$action(...$params);
    }
}
