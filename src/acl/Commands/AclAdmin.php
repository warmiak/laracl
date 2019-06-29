<?php

namespace App\Console\Commands;
use App\Profile;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Console\Command;

class AclAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Admin Account.';

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
        /*        Welcome Screen       */
        /*******************************/
        $this->line(''); $this->line('');
        $this->question(' ----------------------------------- ');
        $this->question('          Create Admin Account       ');
        $this->question(' ----------------------------------- ');
        $this->line(''); $this->line('');

        do {
            $name = $this->ask('Enter the Username for the Admin Account');
            $validator = Validator::make(['name' => $name], ['name' => 'required|alpha']);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $this->error($errors->first('name'));
            }
        } while ($validator->fails());

        do {
            $email = $this->ask('Enter the E-Mail for the Admin Account');
            $validator = Validator::make(['email' => $email], ['email' => 'required|email']);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $this->error($errors->first('email'));
            }
        } while ($validator->fails());

        do {
            $password = $this->ask('Enter the Password for the Admin Account');
            $validator = Validator::make(['password' => $password], ['password' => 'required|min:8']);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $this->error($errors->first('password'));
            }
        } while ($validator->fails());


        $adminAccount = [
            [$name, $email, $password]
        ];

        $this->table(['Username', 'E-Mail', 'Password'], $adminAccount);

        if (!$this->confirm('Is this Account Information correct ?')) {
            $this->error(' ------------------------------------------ ');
            $this->error('      Operation canceled. Nothing done.     ');
            $this->error(' ------------------------------------------ ');
            $this->line('');
            die();
        }

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->active = true;
        $user->confirmed = true;
        $user->save();

        $user->giveRoleTo(config('acl.default_admin_role'));
        $user->giveRoleTo(config('acl.default_member_role'));

        $profile = new Profile;
        $profile->user_id = $user->id;
        $profile->profilename = $user->name;
        $profile->save();

        Log::warning('Admin Account was created. Username: '.$name.' E-Mail: '.$email);

        $this->line('');
        $this->info('Admin Account created.');
        $this->line('');
        $this->error('Clear your Terminal History for Security !');
        $this->line('');
    }
}
