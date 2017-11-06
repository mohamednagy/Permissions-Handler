# Permissions Handler
Permissions-handler is an easy-to-use third party package for laravel 5  to manage users roles and permissions  using [annotaions](https://github.com/doctrine/annotations).


### Installation

add the package to your composer.json 
    
    "nagy/permissions-handler":"1.0"
    
 in your config/app.php add

    'providers' => [
        ...
        PermissionsHandler\PermissionsHandlerServiceProvider::class
    ],

    'aliases'   => [
        ...
        'PermissionsHandler' => PermissionsHandler\Facades\PermissionsHandlerFacade::class,
    ]


    
Publish the package configrations

    php artisan vendor:publish --provider=PermissionsHandler\PermissionsHandlerServiceProvider

This will generate *tables migrations* and *config/permissoinsHandler.php*.

Run the new migrations

    php artisan migrate

    
Include `CanDo` trait into your User model

    use PermissionsHandler\CanDo;

    class User extends Model
    {
        use CanDo;
    

### Config
    /**
    * A model which PermissionsHandler handle
    */
    'user' => \App\User::class


    /**
    * redirect url in case of the user doesn't authorized to do action
    */
    'redirectUrl' => null,


    /**
    * Aggressive Mode define the attitude of PermissionsHandler for handling permissions
    * True: means that the the method SHOULD has permissions written in its DocBlock and the user should has at least on of those permissions to allow acccess to this method
    * False: if there are no permissions for this method then its considered as a public and PermissionsHandler will allow access
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

PermissionsHandler uses annotaions ([doctrine/annotations](https://github.com/doctrine/annotations)) written in controller methods to read permissoins then check whether the user has permissions or not 

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
