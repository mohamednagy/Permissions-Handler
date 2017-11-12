<?php

namespace PermissionsHandler\Commands;

use Illuminate\Console\Command;
use PermissionsHandler;

class ClearAnnotationsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:clear-cached-annotations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cached annotations';

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
        PermissionsHandler::clearCachedAnnotations();
        $this->info('All is done!');
    }
}
