<?php

namespace App\Console\Commands;

use App\Services\AutoAssignService;
use Illuminate\Console\Command;

class AutoAssignRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:auto-assign {--minutes=10 : Admin inactivity threshold in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically assign pending requests to nearest shelters when admin is inactive';

    /**
     * Execute the console command.
     */
    public function handle(AutoAssignService $autoAssignService)
    {
        $this->info('🔄 Starting auto-assignment process...');
        
        $minutes = (int) $this->option('minutes');
        $this->info("⏱️  Admin inactivity threshold: {$minutes} minutes");

        // Get statistics before assignment
        $stats = $autoAssignService->getStatistics();
        $this->line("\n📊 Current Status:");
        $this->line("   • Pending Requests: {$stats['pending_requests']}");
        $this->line("   • Available Shelters: {$stats['available_shelters']}");
        $this->line("   • Total Capacity: {$stats['total_available_capacity']} people");
        $this->line("   • Last Admin Activity: {$stats['last_admin_activity']}");
        $this->line("   • Admin Status: {$stats['admin_status']}");

        // Run auto-assignment
        $results = $autoAssignService->assignPendingRequests($minutes);

        $this->line("\n📝 Assignment Results:");
        $this->line("   • Checked At: {$results['checked_at']}");
        $this->line("   • Admin Inactive: " . ($results['admin_inactive'] ? 'Yes' : 'No'));
        $this->line("   • Pending Requests Found: {$results['pending_requests']}");
        
        if ($results['assigned'] > 0) {
            $this->info("   ✅ Successfully Assigned: {$results['assigned']}");
        }
        
        if ($results['failed'] > 0) {
            $this->error("   ❌ Failed: {$results['failed']}");
            
            if (!empty($results['errors'])) {
                $this->line("\n⚠️  Errors:");
                foreach ($results['errors'] as $error) {
                    $this->error("   • {$error}");
                }
            }
        }

        $this->line("\n" . $results['message']);
        
        return $results['failed'] === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
