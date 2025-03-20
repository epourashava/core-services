<?php

namespace App\Console\Commands;

use Core\Models\Municipality;
use Spatie\Permission\Models\Permission as PermissionModel;
use Core\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\confirm;

class AppTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tenant {action?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or assign a tenant.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        if (!$action) {
            $action = $this->choice('What do you want to do?', [
                'create',
                'assign'
            ]);
        }

        if ($action === 'create') {
            $this->createTenant();
        } elseif ($action === 'assign') {
            $this->assignTenant();
        } else {
            $this->error('Invalid action.');
        }
    }

    function createTenant()
    {
        $this->info('Please provide the following information:');
        $name = $this->ask('What is the name of the tenant?');
        $bengaliName = $this->ask('What is the name of the tenant (Bengali)?');
        $subdomain = $this->ask('What is the subdomain of the tenant?');
        DB::beginTransaction();

        $tenant = Municipality::factory()->create([
            'name' => $name,
            'name_bn' => $bengaliName,
            'subdomain' => trim($subdomain),
        ]);

        $assignUser = confirm(label: 'Do you want to assign a user to this tenant?');

        $this->attachPermissions($tenant);

        if ($assignUser) {
            $email = $this->ask('What is the email of the user?');
            $user = User::withoutTenant()->where('email', $email)->first();

            if (!$user) {
                $this->error('User not found.');
                DB::rollBack();
                return;
            }
            $tenant->users()->attach($user);

            $this->info('User assigned to tenant successfully.');
        }

        DB::commit();

        $this->info('Tenant created successfully.');
    }

    function assignTenant()
    {
        $this->info('Please provide the following information:');
        $subdomain = $this->ask('What is the subdomain of your tenant?');
        $tenant = Municipality::whereSubdomain($subdomain)->first();
        if (!$tenant) {
            $this->error("Tenant not found!\n");
            return;
        }

        $email = $this->ask('What is the email of the user?');
        $user = User::withoutTenant()->where('email', $email)->first();

        if (!$user) {
            $this->error('User not found.');
            return;
        }
        $tenant->users()->attach($user);

        $this->info('User assigned to tenant successfully.');
    }

    private function attachPermissions($tenant)
    {
        $permissions = config('core.permissions', []);

        foreach ($permissions as $permission) {
            PermissionModel::query()->firstOrCreate([
                'name' => $permission,
                'subdomain' => $tenant->subdomain,
            ], [
                'guard_name' => config('auth.defaults.guard', 'web'),
            ]);
        }
    }
}
