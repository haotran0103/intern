<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\user_activity;
class UserHistoryCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user-history-commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $aMonthAgo = now()->subDays(30);
        user_activity::onlyTrashed()
            ->where('deleted_at', '<', $aMonthAgo)
            ->forceDelete(); // Xóa vĩnh viễn khỏi thùng rác
        $this->info('Deleted posts older than 7 days.');
    }
}
