<?php

namespace PermissionsHandler\Commands;

use PermissionsHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:clear-cache {--annotations} {--db}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cached annotaions or database';

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
        $annotations = $this->option('annotations');
        $db = $this->option('db');

        if ($annotations) {
            PermissionsHandler::clearCachedAnnotations();
            $this->info('Annotations cache has been deleted!');
        }

        if ($db) {
            Cache::forget('permissionsHandler');
            $this->info('Database cache has been deleted!');
        }
    }
}
