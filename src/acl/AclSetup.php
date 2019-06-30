<?php

namespace Orendo\LaravelPackage\Acl;

use App\Mail\TestMail;
use App\Permission;
use App\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Orendo\LaravelPackage\Acl\Assets;
use Orendo\LaravelPackage\Acl\Database;

class AclSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the Application';

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
        /*******************************/
        /*  Check Database Connection  */
        /*******************************/
        try {
            DB::connection()->getPdo();
        } catch (\PDOException $e) {
            $this->line('');
            $this->error('Warning: Your Database Connection is not working !');
            $this->error('Exception: '.$e->getMessage());
            $this->line('');
            die();
        }

        /*******************************/
        /*        Welcome Screen       */
        /*******************************/
        $this->line(''); $this->line('');
        $this->question(' ----------------------------------- ');
        $this->question('          Laravel Acl Setup          ');
        $this->question(' ----------------------------------- ');
        $this->line(''); $this->line('');

        /*******************************/
        /*  Confirm Laravel ACL Setup  */
        /*******************************/
        if (!$this->confirm('Do you want to setup Laravel ?')) {
        $this->error(' ------------------------------------------ ');
        $this->error(' Laravel Acl Setup canceled ! Nothing done. ');
        $this->error(' ------------------------------------------ ');
        $this->line('');
        die();
    }

        /********************/
        /*  Install Assets  */
        /********************/

        Assets::installAssets();
        $this->info(html_entity_decode("&#10003;") .' Assets installed successful. ');

        $this->call('storage:link');

        if (empty(env('APP_KEY'))) {
            $this->call('key:generate');
        }

        return Log::info('Assets successful installed.');

        /**********************/
        /*  Migrate Database  */
        /**********************/

        Schema::connection('mysql')->dropAllTables();

        $this->call('migrate:refresh');

        Log::debug('ACL: Database Migration successful installed.');

        /**********************************/
        /*  Create Roles and Permissions  */
        /**********************************/

        Database::installRolePermission();

        Log::debug('ACL: Default Application Roles and Permissions successful installed');

        Assets::publishAuthServiceProvider();

        /************/
        /*  Output  */
        /************/

        $permissionsTable = Permission::all(['name', 'label'])->toArray();
        $rolesTable = Role::all(['name', 'label'])->toArray();
        $header = ['Name', 'Label'];

        $this->comment('Roles Overview:');
        $this->table($header, $rolesTable);
        $this->line('');
        $this->comment('Permissions Overview:');
        $this->table($header, $permissionsTable);

        $this->line('');
        $this->question(' Time to build some awesome things ! Isn\'t it ? ');
        $this->line('');

        Log::info('ACL: Laravel ACL System successful installed.');

    }
}
