<?php
require_once dirname(__DIR__) . '/Config/constants.php';
require_once BASE_DIR . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(BASE_DIR);
$dotenv->load();

echo strripos("SELECT  *  FROM users","SEL");
$obj= new Core\Model();


//$create=$obj-> create( [ 'mama', '123', 'lida@gmail.com']);
//$update= $obj -> update([ 'lilinda', '123', 'anna@gmail.com'], '33');
//$delete=$obj-> delete( '30');
$select=$obj->select()->where('31','id','=')->get();
var_dump($select);
//var_dump($obj-> getTableFields('users'));



//dd(Core\Model::findBy('2','l'));

if (!session_id()) {
    session_start();
}

try {
    $router = new \Core\Router();

    require_once BASE_DIR . '/routes/web.php';

//    if (!preg_match('/assets/i', $_SERVER['REQUEST_URI'])) {
        $router->dispatch($_SERVER['REQUEST_URI']);
//    }
} catch (Exception $exception) {
    dd($exception->getMessage());
}
