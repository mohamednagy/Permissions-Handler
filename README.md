##### Package is under development

# Permissions Handler

Permissions-handler is an easy-to-use third party package for laravel 5.0+ to manage users roles and permissions  using **annotations**.


### Installation

add the package to your composer.json 
    
    "nagy/permissions-handler":"dev-master"
    
 in your config/app.php add

    'PermissionsHandler\PermissionsHandlerServiceProvider',
    'PermissionsHandler' => 'PermissionsHandler\PermissionsHandlerInterface'
    
Publish the package configrations

    php artisan vendor:publish
    
This will generate *tables migrations*, *App/Http/Middlware/PermissionsHandlerMiddleware*, *Models* and *config/permissionsConfig.php*.

Run the new migrations

    php artisan migrate

In the **config/permissionsConfig.php**  set the path to your *User* model
	
	user => 'App\User'

### Usage
Suppose that you have Blog system which **admins** can add **posts** and **visitors** can **comment** on this posts. So, you have tow main gropus:
    
   * Admins
   * Visitors

and you have two main sections:
    
   * Posts
   * Comments

So the roles will be as the following:
 - Admins can:
    - add, edit and delete Posts
    - edit and delete comments
- Visitors can:
    - add, edit and delete comments

#### Database seeding
* Divide your system to section for example *posts* is a section and *comments* is a section.
* Create the permissions needed for each section for example *posts* needs permissions like *add*, *edit* and *delete*.
* Assign the permissions created to the section thats created for it.
* Create your groups for example *admins*.
* Now you have permissions and groups, assign the permissions to groups.
* Assign the users to groups.

#### With routes
	/**
	* @permissions ["posts.add"]
	*/
	function store(Request $request){
	 // you code here
	}
**permissions-handler** uses a combination form  *section name* and *permission* to specify the permissions for a this route. So that's mean,Only  the logged user whose has group that has the permission **add** in section **posts** can access this function.

#### With Views

	<button permissions="posts.add">Edit</Button>
	
**permissions-handler** will filter the views against *permissions* before it send to the client, If the user does not has the permission then the element will be removed.


##### Great Thanks to Ahmed Sorour.
