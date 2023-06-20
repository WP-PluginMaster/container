<?php declare(strict_types=1);

require_once  './vendor/autoload.php';

require_once './test/Project.php';
require_once './test/User.php';


$container = new \PluginMaster\Container\Container();


$container->set('User', function () {
    return new User();
});

/** @var User $user */
$user = $container->get('User') ;
$user->setName('Afiqul Islam');
echo $user->getName().PHP_EOL;


/** @var User $user */
$user = $container->call('Project@user', ['name' => 'PHP IS POWER']);
echo $user->getName().PHP_EOL;


/** @var User $user */
$user = $container->call('Project#user', ['name' => 'AL EMRAN'], ['methodSeparator'=>'#']);

echo $user->getName().PHP_EOL;


/** @var User $user */
$user = $container->call([Project::class, 'user'], ['name' => 'AL EMRAN']);

echo $user->getName().PHP_EOL;


/** @var Project $user */
$user = $container->make(Project::class, ['name' => 'Make User', 'user' => new User('Hasan')]);
if($container->has(Project::class)) {
    echo $container->get(Project::class)->user()->getname().PHP_EOL;
}


/** @var User $user */
$user = $container->get(User::class);

$user->setName('Get User');
echo $user->getName().PHP_EOL;
