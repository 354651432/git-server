<?php

include_once "App.php";
include_once "Router.php";
include_once "Git.php";

$router = new Router;
$app = new App;


$app->gitRoot = 'repos';// 设置 git 仓库目录，用于服务器端存放各 git 仓库

// get repo info/refs
$router->any(['get', 'head'], '/*\.git/info/refs', [$app, 'getInfoRefs']);

$router->post('/*\.git/git-[a-z]+-pack', [$app, 'command']);

// access file contents
//$router->any(['get', 'head'], '/*\.git/*', function () {
//    return false;
//});

$router->post('/create', [$app, 'init']);


$router->run();
