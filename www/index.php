<?php
define('APPLICATION_PATH', __DIR__ . "/..");

require APPLICATION_PATH . '/vendor/autoload.php';

\Tracy\Debugger::enable();

//RobotLoader

$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory(APPLICATION_PATH . '/src');
$loader->addDirectory(APPLICATION_PATH . '/controllers');
$loader->setTempDirectory(new Nette\Caching\Storages\FileStorage(APPLICATION_PATH . '/temp'));
$loader->register();


//zero router - is this api request?

$uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$options = [
    'server' => 'http://raml-server.127.0.0.1.xip.io',
    'apiUriPart' => 'api',
    'ramlDir' => APPLICATION_PATH . '/raml',
    'ramlUriPart' => 'raml',
    'controllerNameSpace' => 'App\\Api'
];

// cache

$fileStorage = new \Nette\Caching\Storages\FileStorage(APPLICATION_PATH . '/temp');
$cache = new \Nette\Caching\Cache($fileStorage);

$router = new \RamlServer\ZeroRouter($options, $uri);
$router->setCache($cache);

//if not api url, serve some other content - typically, nette router gets in now

if ($router->isApiRequest()) {
    $router->serveApi();
    exit();
}

//or serve the raml files

if ($router->isRamlRequest()) {
    $router->serveRamlFiles();
    exit();
}

//serve other than api content

header('Content-Type: text/markdown');
echo file_get_contents("../README.md");
die();

