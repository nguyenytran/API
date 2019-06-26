<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Passport\Client;

class InstallCommand extends Command
{
    protected $signature = 'api:install {--force : Force re-install} {--seed : Seed data}';

    protected $description = 'Run the commands necessary to prepare api for use';

    public function handle()
    {
        $this->info('Call passport key generation command');
        $this->call('passport:keys', ['--force' => $this->option('force')]);

        if (!Client::query()->first()) {
            $this->info('Call passport client generation command');
            $this->call('passport:client', ['--password' => true, '--name' => 'API']);
        }
    }
}
