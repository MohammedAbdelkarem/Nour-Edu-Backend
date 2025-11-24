<?php

namespace App\Console\Commands\Users;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteUnverifiedAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-unverified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to delete all unverified users account (start register but never verify using otp) after specific time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::query()
            ->whereNull('account_verified_at')
            ->where("created_at", "<=", Carbon::now()->subWeek()->format("Y-m-d"))
            ->forceDelete();
    }
}
