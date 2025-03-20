<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserAction extends Command
{
    const ACTION_MARK_EMAIL_AS_VERIFIED = 'Mark email as verified';
    const ACTION_VIEW_USER = 'View user';
    const ACTION_UPDATE_PASSWORD = 'Update password';
    const ACTION_UPDATE_DATA = 'Update data';
    const ACTION_DELETE_USER = 'Delete user';
    const EXIT = 'Exit';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user:action {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do some action on user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userEmail = $this->option('email');
        if (!$userEmail) {
            $userEmail = $this->ask('What is the email of the user?');
        }

        $this->runTheAction($userEmail);
    }

    function runTheAction($userEmail)
    {
        $user = User::withoutTenant()->where('email', $userEmail)->first();
        if (!$user) {
            $this->error('User not found.');
            $confirm = $this->confirm('Do you want to create a new user?');

            if ($confirm) {
                $name = $this->ask('Enter the name');
                $password = $this->secret('Enter the password');

                $user = User::factory()->create([
                    'name' => $name,
                    'email' => $userEmail,
                    'password' => bcrypt($password),
                ]);

                $this->info('User created.');

                $this->runTheAction($userEmail);
            }

            return;
        }

        $action = $this->getAction();

        switch ($action) {
            case self::ACTION_MARK_EMAIL_AS_VERIFIED:
                $this->info('Marking email as verified...');
                $user->markEmailAsVerified();
                $this->info('Email marked as verified.');
                break;

            case self::ACTION_VIEW_USER:
                foreach ($user->toArray() as $key => $value) {
                    $this->info($key . ': ' . "$value");
                }
                break;

            case self::ACTION_UPDATE_PASSWORD:
                $password = $this->secret('Enter the new password');
                $user->password = bcrypt($password);
                $user->save();
                $this->info('Password updated.');
                break;

            case self::ACTION_UPDATE_DATA:
                $this->updateData($user);
                break;

            case self::ACTION_DELETE_USER:
                $confirm = $this->confirm('Are you sure you want to delete the user?');
                if ($confirm) {
                    $user->delete();
                    $this->info('User deleted.');

                    exit("Bye!\n");
                    break;
                }
                break;

            case self::EXIT:
                exit("Bye!\n");
                break;

            default:
                $this->error('Action not found.');
                $this->runTheAction($userEmail);
                break;
        }

        $this->runTheAction($userEmail);
    }

    private function updateData(User $user)
    {
        $column = $this->choice('Enter the column name', $user->getFillable());

        $value = $this->ask('Enter the value');

        $user->update([
            $column => $value
        ]);

        $this->info('Data updated.');
    }

    private function getAction()
    {
        return $this->choice('What do you want to do?', [
            self::ACTION_VIEW_USER,
            self::ACTION_MARK_EMAIL_AS_VERIFIED,
            self::ACTION_UPDATE_PASSWORD,
            self::ACTION_UPDATE_DATA,
            self::ACTION_DELETE_USER,
            self::EXIT,
        ]);
    }
}
