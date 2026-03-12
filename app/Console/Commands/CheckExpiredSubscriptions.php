<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:check-expired-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    protected $signature   = 'subscriptions:check';
    protected $description = 'Check for expired subscriptions and suspend schools after grace period';
    public function handle(SubscriptionService $service)
    {
        //
        $this->info('Checking subscriptions...');

        $result = $service->checkExpiredSubscriptions();

        $this->info("✅ Done.");
        $this->line("  → {$result['warned']} school(s) in grace period (warning)");
        $this->line("  → {$result['banned']} school(s) suspended (grace period over)");
    }
}
