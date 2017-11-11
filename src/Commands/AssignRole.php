<?php

namespace PermissionsHandler\Commands;

use PermissionsHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Models\Permission;

class AssignRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:assign {--role=} {--user-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign role to user --role=admin --use-id=2';


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
        $userId = $this->option('user-id');
        $roleName = $this->option('role');
        if(!$userId || !$roleName){
            $this->error('both user-id and role are required');
            return;
        }
        $this->line("Assigning role `$roleName` to user `$userId`");
        $user = PermissionsHandler::user($userId);
        $role = PermissionsHandler::addRole($roleName);
        PermissionsHandler::assignRoleToUser($role, $user);
        $this->info('role has been assigned!');

    }
}

?>