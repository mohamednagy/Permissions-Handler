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
        $role = $this->option('role');
        if(!$userId || !$role){
            $this->error('user-id and role is required');
            return;
        }
        $this->line("Assigning role `$role` to user `$userId`");
        $user = PermissionsHandler::getUser($userId);
        PermissionsHandler::assignUserToRole($user, $role);
        $this->info('permission has been created!');

    }
}

?>