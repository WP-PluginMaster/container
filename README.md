### Simplified Dependency Injection Container For PluginMaster

#### Simplified dependency injection container for PHP project. We create this package for PluginMaster(WordPress Plugin Development Framework)


Example:

```injectablephp

$container = new \PluginMaster\Container\Container();

```

#### 1. Set Alias

```injectablephp

$container->set('User', function () {
    return new User();
});
```

#### 2. Get Alias

```injectablephp 
$user = $container->get('User') ;
```
#### 3. Call A method of class or callback function

```injectablephp 
$container->call('Project@user', ['name' => 'PHP IS POWER' ]);
```

```injectablephp 
 $container->call('Project#user', ['name' => 'AL EMRAN' ], ['methodSeparator'=>'#']);
```

```injectablephp
$container->call([Project::class, 'user'], ['name' => 'AL EMRAN' ]);
```

#### 4. Make A Object from Class

```injectablephp
$project = $container->make(Project::class, ['name' => 'Make User' ]);
```

```injectablephp
$user = $container->make(User::class);
```

#### 5. get Object from provided class

```injectablephp
$user = $container->get(User::class);
``` 
#### 6. Check Resolved Object or alias exist
```injectablephp
$user = $container->has(User::class);
```


### Test

### 1. Clone Repo
```shell
git clone ''
```

#### 2. Install Dependency
```shell
composer install
```

#### 3. Run Index.php file
```php 
php Index.php
```

