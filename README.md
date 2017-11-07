# Permissions Handler
Permissions-handler is an easy-to-use third party package for laravel 5  to manage users roles and permissions  using [annotations](https://github.com/doctrine/annotations).


### Installation

require the package 
    
    $ composer require nagy/permissions-handler
    
 in your config/app.php add

    'providers' => [
        ...
        PermissionsHandler\PermissionsHandlerServiceProvider::class
    ],

    'aliases'   => [
        ...
        'PermissionsHandler' => PermissionsHandler\Facades\PermissionsHandlerFacade::class,
    ]

> You don't need this step in laravel5.5 `package:discover`  will do the job :)
    
Publish the package configurations

    php artisan vendor:publish --provider=PermissionsHandler\PermissionsHandlerServiceProvider

This will generate *tables migrations* and *config/permissionsHandler.php*.

Run the new migrations

    php artisan migrate

    
Include `CanDo` trait into your User model

    use PermissionsHandler\CanDo;

    class User extends Model
    {
        use CanDo;
    

### Config

    /**
    * redirect url in case of the user doesn't authorized to do action
    */
    'redirectUrl' => null,


    **
    * Aggressive Mode define the attitude of PermissionsHandler for handling permissions
    * True:    1- Method SHOULD has permissions assiged to allow access, if no, then this method considered as a private and no direct access is allowed
    *          2- User SHOULD has all the permissions assiged to this method to allow access
    * False:   1- If there are no permissions for this method then its considered as a public and PermissionsHandler will allow access
    *          2- If the user has one of the assigned permissions to the method then allow access
   */
    'aggressiveMode' => false,


    /**
    * exlude routes paths from PermissionsHandler hands
    */
    'excludedRoutes' => [
        'login', 'register'
    ]

### Usage
 * Register PermissionsHandlerMiddleware to be able to handle permissions for controller methods
    `PermissionsHandler\Middleware/PermissionsHandlerMiddleware::class`

PermissionsHandler uses annotations ([doctrine/annotations](https://github.com/doctrine/annotations)) written in controller methods to read permissions then check whether the user has permissions or not 

#### With controller methods
	/**
	* @PermissoinsHandler\Permissoins({"add-users"})
	*/
	function store(Request $request){
	 // your code here
	}

or within your code

    Auth::user()->canDo('add-users');
#### With Views
you can use `@canDo` Blade directive to check if the user has a permissions within your blade as the following:

    @canDo(['edit-suer'])
        can edit use
    @elsecanDo(['delete-user'])
        can delete user
    @else
        user has no permissions
    @endcanDo
