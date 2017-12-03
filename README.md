## Usage

### With routes and methods
Permissions Handler comes with three middlewares `MethodMiddleware`, `PermissionMiddleware` and `RoleMiddleware`
#### **MethodMiddleware**
This middleware will check for annotations written for your methods and check the permisions and roles assigned to this method, register this middleware into your `kernel.php` under `$middleware` to use it globaly over your system or `middlewareGroups` to use over groups or register it anywhere else according to you needs.
```
/**
* @Permissions({"add-posts"})
*/
function store(Request $request){
    // your code here
}

/**
* @Roles({"admin"})
*/
function delete(Request $request){
    // your code here
}

 
/**
* @Owns(relation="posts", attribute="id")
*/
function update(Request $request){
    // your code here
}

```
`Permissions` and `Roles` accepts array of permissions and roles in format `{"add-users", "view-users"}` and `{"super-admin", "admin"}` respectively. <br>

You can pass extra optional parameter `requireAll=true` (default is *false*) to enforce the user to has all the permissions and roles to be able to access the method, for example
```
/**
* @Permissions({"add-posts", "edit-posts"}, requireAll=true)
*/
function store(Request $request){
    // your code here
}
```

`Owns` annotation dives more deeper, its used to ensure that the user owns a specific resource, for example, only the post owner can edit the post, so you need to ensure that the user own the post via a relation.<br>
**Paramters** <br>
* relation (required)<br>
the eloquent relation
* attribute (required) <br>
the request paramter which PermissionsHandler get its value from the request, for example `attribute="id"` then PermissionsHandler will search for parameter "id" in the request `$request->id`.
* key (optional) <br>
```$user->{$this->relation}->contains($this->key, $request->{$this->attribute});``` <br>
as you see `key` is the attribute that we search by. by default the `attribute` paramter is the `key` paramters unless you decide something else then you have to deine your `attribute` and `key` parameter.



#### **PermissionMiddleware**
This middleware used to handle the permissions for specific groups or routes.<br>
register the route into the kernel.php 
```
protected $routeMiddleware = [
    'permissions' => PermissionsHandler\Middleware\PermissionMiddleware::class
]
```

then you can use it as the following:

```
// permissions
Route::group(['middleware' => ['permissions:add-user']], function () {
    //
});
```
If you need to pass more than one permission, you can pass them with the following format `add-user|edit-user`. <br>
The middleware accepts additional boolean parameter `$requireAll`, it validates that the user must has all the permissions in the first parameter, default is `false`.


#### **RoleMiddleware**
This middleware used to handle the roles for specific groups or routes.<br>
It's working the same as `PermissionsMiddleware`


### Blade Directives
`PermissionsHandler` comes with some useful blade directives

```
@canDo('edit-user')
  current authenticated user has edit-user permission
@endcanDo

@permission('delete-users') // @permission(['edit-users', 'view-users']) or @permission(['edit-users', 'view-users'], true)
   current authenticated user has edit-user permission
@endpermission

@role('admin') // @role(['edit-users', 'view-users']) or rRole(['edit-users', 'view-users'], true)
 current authenticated user has admin role
@endrole
```

### Within your code
* **User**
```
/**
* accepts string
*/
$canDo = $user->canDo('add-users');
```
```
$hasPermission = $user->hasPermission('add-users'); 
// or has one of any permission
$hasPermission = $user->hasPermission(['add-users', 'view-users]);
// or has all the permissions
$hasPermission = $user->hasPermission(['add-users', 'view-users], true);
```
```
$hasRole = $user->hasRole('admin');
// or has one of any role
$hasRole = $user->hasRole(['user', 'admin]);
// or has all the roles
$hasRole = $user->hasRole(['user', 'admin], true);
```
```
/** 
 * accepts model or collection
 */
$user->assignRole(Role::whereName('admin')->first());
```
```
/** 
 * accepts model or collection
 */
$adminRole = Role::whereName('admin')->first();
$user->unAssignRole($adminRole);
```
```
/**
 * remove all the assigned roles
 */
$user->unAssignAllRoles();
```

* **Role**
```
/** 
 * accepts string or model
 */
$role->hasPermission('perm1');
```
```
/**
 * accepts model or collection
 */
$role->assignPermission(Permission::find(1));
```
```
/**
 * accepts model or collection
 */
$role->unAssignPermission(Permission::find(1));
```
```
/**
 * unassign all permissions
 */
$role->unAssignAllPermissions();
```

### Artisan commands
* Add permission <br>
`php artisan permissions:add --permission=add-users`

* Add Role <br>
`php artisan permissions:add --role=admin`

* Assign permission to role <br>
`php artisan permissions:assign --role=admin --permission=add-users`

* Assign role to user <br>
`php artisan permissions:assign --role=admin --user-id=5`

* Clear cached annoations <br>
`php artisan permissions:clear-cached-annotations`