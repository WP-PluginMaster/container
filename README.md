## Simplified Dependency Injection Container For PluginMaster (Or any PHP project)

##### Simplified dependency injection container for PHP project. We create this package for PluginMaster(WordPress Plugin Development Framework)

 *Dependency Injection Containers are mainly used for managing the dependencies required by classes and their methods. When we need to create an instance of a class or call a method of a class, a container helps us deal with these dependencies smoothly.*

**I've developed this new PHP container with the goal of streamlining and simplifying dependency management. The functions it provides:**

1. **get:** This function allows you to retrieve an instance of a class or an alias that's already been defined.


2. **make:** for creating a new instance of a class, using any necessary parameter also it will replace resolved objects that created with provided class name.


3. **call:** To invoke a method of a class. It can take callable arguments in various formats like 

      a. ```[class::class, 'method']```<br>
      b. ```[new class(), 'method']```<br>
      c. ```'class@method'```<br>
      d. ```class::class``` ( will fire _invoke method).


4. **set:** Assigns an alias to a class or a callback function.
has: Checks if the container has resolved a certain class or alias.

In short, I developed this new PHP container to simplify dependency management, making it more accessible and straightforward, especially for projects that don't require more advanced features.


#### Install Package: 
```shell
composer require plugin-master/container
```

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
git clone https://github.com/WP-PluginMaster/container.git
```

#### 2. Install Dependency
```shell
composer install
```

#### 3. Run Index.php file
```php 
php Index.php
```

