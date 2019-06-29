<?php

namespace App\Console\Commands;

use App\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AclPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Permission Table';

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
        if (!Schema::hasTable('permissions')) {
            $this->error('Table permissions not found.');
        }

        $appPermissions = config('acl.application_permissions');

        foreach ($appPermissions as $value) {
            Permission::updateOrCreate(
                ['name' => $value[0]],
                ['label' => $value[1]]
            );
        }

        Log::warning('Laravel ACL Permissions updated');
        $this->info('Permissions updated.');

    }
}
