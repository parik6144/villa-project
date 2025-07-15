<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CustomVerifyEmail;
use App\Models\User;

class TestEmailConfiguration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : Email address to send test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration and send a sample verification email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter email address to test');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }

        $this->info('Testing email configuration...');

        try {
            // Test basic email sending
            Mail::raw('This is a test email from Villa4You Club', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Villa4You Club');
            });

            $this->info('✓ Basic email test sent successfully');

            // Test verification email with a dummy user
            $testUser = new User([
                'name' => 'Test',
                'last_name' => 'User',
                'email' => $email,
                'id' => 999999 // Dummy ID for testing
            ]);

            $testUser->notify(new CustomVerifyEmail());

            $this->info('✓ Verification email test sent successfully');
            $this->info("Check the inbox for: {$email}");
            $this->warn('Note: The verification link in the test email will not work as it uses a dummy user.');

        } catch (\Exception $e) {
            $this->error('Email test failed: ' . $e->getMessage());
            $this->info('Please check your email configuration in .env file');
            return 1;
        }

        return 0;
    }
}