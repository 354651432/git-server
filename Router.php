<?php


class Router
{
    public $patterns = [];

    public function get($pattern, $callback)
    {
        $this->patterns[] = ['GET', $pattern, $callback];
    }

    public function post($pattern, $callback)
    {
        $this->patterns[] = ['POST', $pattern, $callback];
    }

    public function head($pattern, $callback)
    {
        $this->patterns[] = ['HEAD', $pattern, $callback];
    }

    public function any($methods, $pattern, $callback)
    {
        $methods || $methods = ['get', 'head', 'post'];
        foreach ($methods as $method) {
            call_user_func([$this, $method], $pattern, $callback);
        }
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['PHP_SELF'];

        foreach ($this->patterns as $pattern) {
            if ($method == $pattern[0] && preg_match("~{$pattern[1]}~ims", $uri, $match)) {
                $log = sprintf('%s [%s] %s', date('Y-m-d H:i:s'), $method, $_SERVER['REQUEST_URI']) . PHP_EOL;
                file_put_contents('log.data', $log, FILE_APPEND);
                call_user_func($pattern[2]);
                return false;
            }
        }
    }
}

