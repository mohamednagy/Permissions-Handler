<?php

namespace PermissionsHandler\Commands;

use Illuminate\Console\Command;
use PermissionsHandler;

class AddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:add {--permission=} {--role=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new permission to the system database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $permissionName = $this->option('permission');
        $roleName = $this->option('role');

        if (!$permissionName && !$roleName) {
            $this->error('permission or role is required');

            return;
        }

        $userModel = PermissionsHandler::user();

        if($permissionName){
            $permission = PermissionsHandler::addPermission($permissionName);
            $this->info('Permission has been created!');
        }
        if($roleName){
            $role = PermissionsHandler::addRole($roleName);
            $this->info('Role has been created!');
        }
    }
}
