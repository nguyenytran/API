<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\AuthCode;
use Laravel\Passport\Client;
use Laravel\Passport\PersonalAccessClient;
use Laravel\Passport\Token;

class ReInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:reinstall {--force : Force re-install} {--seed : Seed data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the commands necessary to prepare api for use';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $confirm = $this->confirm('Drop api feature and re-install api, are you sure?');
        if (!$confirm) {
            $this->line('Action cancelled');
            return;
        }

        $this->clear();

        $this->call('api:install', [
            '--force' => $this->option('force'),
            '--seed' => $this->option('seed')
        ]);
    }

    /**
     * Truncate all api data
     */
    protected function clear()
    {
        Token::query()->truncate();
        AuthCode::query()->truncate();
        Client::query()->truncate();
        PersonalAccessClient::query()->truncate();
        DB::table('oauth_refresh_tokens')->truncate();
    }
}
