<?php


return [
  /*
   * user model which permissionsHandler will play with
   */
  'user' => \App\User::class,

  /*
   * redirect url in case of the user doesn't authorized to do action
   */
  'redirectUrl' => null,

  /*
   * Aggressive Mode define the attitude of PermissionsHandler for handling permissions
   * True:    1- Method SHOULD has permissions assiged to allow access, if no, then this method considered as a private and no direct access is allowed
   *          2- User SHOULD has all the permissions assiged to this method to allow access
   * False:   1- If there are no permissions for this method then its considered as a public and PermissionsHandler will allow access
   *          2- If the user has one of the assigned permissions to the method then allow access
   */
  'aggressiveMode' => false,

  /*
   * exlude routes paths from PermissionsHandler hands
   */
  'excludedRoutes' => [
    'login', 'register',
  ],

  /*
   * Database cache expiration time in minutes
   */
  'cacheExpiration' => 60,

  /**
   * Allow to save the created permissions, roles and role-permission to files
   * to be seeded using artisan command
   */
  'seeder' => true,

];
