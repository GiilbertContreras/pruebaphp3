<?php
    namespace App\Core;

    class Router {
        private $routes = [];

        public function add($method, $path, $handler) {
            $this->routes[] = compact('method','path','handler');
        }
        public function get($path, $handler){ $this->add('GET',$path,$handler); }
        public function post($path, $handler){ $this->add('POST',$path,$handler); }
        public function put($path, $handler){ $this->add('PUT',$path,$handler); }
        public function delete($path, $handler){ $this->add('DELETE',$path,$handler); }

        private function match($method,$uri){
            $uri = parse_url($uri, PHP_URL_PATH);
            foreach($this->routes as $r){
                if ($r['method'] !== $method) continue;
                $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $r['path']);
                $pattern = '#^' . $pattern . '$#';
                if (preg_match($pattern, $uri, $matches)){
                    array_shift($matches);
                    return ['handler'=>$r['handler'],'params'=>$matches];
                }
            }
            return null;
        }

        public function dispatch($method,$uri){
            $m = $this->match($method,$uri);
            if (!$m){ http_response_code(404); echo json_encode(['error'=>'Ruta no encontrada']); return; }
            $h = $m['handler']; $params = $m['params'];
            if (is_callable($h)) { call_user_func_array($h,$params); return; }
            [$class,$method] = $h;
            $controller = new $class();
            call_user_func_array([$controller,$method], $params);
        }
    }
