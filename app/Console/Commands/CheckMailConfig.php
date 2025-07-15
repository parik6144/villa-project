<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckMailConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display current mail configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Current Mail Configuration:');
        $this->line('');

        $mailConfig = config('mail');
        
        $this->table(
            ['Setting', 'Value'],
            [
                ['Default Mailer', $mailConfig['default']],
                ['SMTP Host', $mailConfig['mailers']['smtp']['host']],
                ['SMTP Port', $mailConfig['mailers']['smtp']['port']],
                ['SMTP Username', $mailConfig['mailers']['smtp']['username']],
                ['SMTP Password', $mailConfig['mailers']['smtp']['password'] ? '***HIDDEN***' : 'NOT SET'],
                ['SMTP Encryption', $mailConfig['mailers']['smtp']['encryption']],
                ['From Address', $mailConfig['from']['address']],
                ['From Name', $mailConfig['from']['name']],
            ]
        );

        $this->line('');
        $this->info('Environment Variables:');
        $this->table(
            ['Variable', 'Value'],
            [
                ['MAIL_MAILER', env('MAIL_MAILER', 'NOT SET')],
                ['MAIL_HOST', env('MAIL_HOST', 'NOT SET')],
                ['MAIL_PORT', env('MAIL_PORT', 'NOT SET')],
                ['MAIL_USERNAME', env('MAIL_USERNAME', 'NOT SET')],
                ['MAIL_PASSWORD', env('MAIL_PASSWORD') ? '***HIDDEN***' : 'NOT SET'],
                ['MAIL_ENCRYPTION', env('MAIL_ENCRYPTION', 'NOT SET')],
                ['MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'NOT SET')],
                ['MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'NOT SET')],
            ]
        );

        $this->line('');
        
        // Check for common issues
        $issues = [];
        
        if ($mailConfig['default'] !== 'smtp') {
            $issues[] = 'Default mailer is not set to SMTP';
        }
        
        if (empty($mailConfig['mailers']['smtp']['host'])) {
            $issues[] = 'SMTP host is not configured';
        }
        
        if (empty($mailConfig['mailers']['smtp']['username'])) {
            $issues[] = 'SMTP username is not configured';
        }
        
        if (empty($mailConfig['mailers']['smtp']['password'])) {
            $issues[] = 'SMTP password is not configured';
        }
        
        if (empty($mailConfig['from']['address'])) {
            $issues[] = 'From address is not configured';
        }

        if (empty($issues)) {
            $this->info('✅ Configuration looks good!');
            $this->warn('If emails are not sending, check:');
            $this->line('1. Amazon SES email verification');
            $this->line('2. Amazon SES sandbox mode');
            $this->line('3. Network connectivity');
        } else {
            $this->error('❌ Configuration issues found:');
            foreach ($issues as $issue) {
                $this->line("  - {$issue}");
            }
        }

        return 0;
    }
}