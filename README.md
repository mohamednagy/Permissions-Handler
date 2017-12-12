# Permissions Handler
Permissions-handler is an easy-to-use package for laravel 5  to manage users roles and permissions in an easy and attractive way

* [Installation](https://github.com/mohamednagy/Permissions-Handler/wiki/installation)
* [Config](https://github.com/mohamednagy/Permissions-Handler/wiki/config)
* [Usage](https://github.com/mohamednagy/Permissions-Handler/wiki/usage)
    * [With routes and methods](https://github.com/mohamednagy/Permissions-Handler/wiki/usage#with-routes)
    * [Blade directive](https://github.com/mohamednagy/Permissions-Handler/wiki/usage#blade-directives)
    * [Within your code](https://github.com/mohamednagy/Permissions-Handler/wiki/usage#within-your-code)
    * [Artisan commands](https://github.com/mohamednagy/Permissions-Handler/wiki/usage#artisan-commands)


## Features

 ### Caching
 Permissions Handler uses the caching feature on two levels
 * **Database Cache**: <br>
 Permissions Handler uses the cache driver that configured in your `cache.php` file for caching user permissions, roles for configurable time.
 * **Annotation Cache**: <br>
 Because of parsing the files against the annotations is costy; permission Hander caches the parsed annotations to avoid parsing it again. by default this features is disabled in the development environment and enabled in the production. 

### [Gate](https://laravel.com/docs/5.5/authorization#gates) Integration
PermissionsHandler register users permissions into the `Gate`, so you can easly use laravel built in `can` method.

### Guard Support
PermissionsHandler doesn't depend on a specific model or guard, you can use whatever models or guards you need, PermissionsHandler will handle all.

### Seeder
If you enabled the `seeder` option from the `config/permissionsHandler.php` file then Permissions Handler will save each created permission, role and role-permissions to fils to be able to seed it again in later time or share them with others.

### Annotations
PermissionsHandler comes with an optinal awesome feature **Annotations**, it is based on Doctrine\Annotations. you can use it as the following:
```
use PermissionsHandler\Owns;
use PermissionsHandler\Roles;
use PermissionsHandler\Permissions;
.
.
.

/**
 * @Permissions({"add-posts"})
 */
function store(Request $request) {
    // your code here
}

/**
 * @Roles({"admin"})
 */
function delete(Request $request) {
    // your code here
}

 
/**
 * @Owns(relation="posts", parameter="id")
 */
function update(Request $request) {
    // your code here
}

```

As the above example, Permissions Handler comes with three types of annotations.
 * `Permissions({"perm1"})`: <br> only users that have *perm1* permission can access this method
 * `Roles({"role1"})`: <br> only users that have *role1* role can access this method
 * `Owns(relation="posts", parameter="id")`: <br> for example, if you are the owner of the post then only you whose can edit this post. `Owns` will ensure that, just pass the relation and the parameter which *PermissionsHandler* will get the value from the *Request* accordingly.

 Check the [usage](https://github.com/mohamednagy/Permissions-Handler/wiki/usage) section for more features and details

## License

[MIT License](http://opensource.org/licenses/MIT)
