<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\post_history;
class PostHistoryCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:post-history-commands';

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
        post_history::onlyTrashed()
            ->where('deleted_at', '<', $aMonthAgo)
            ->forceDelete(); // Xóa vĩnh viễn khỏi thùng rác
        $this->info('Deleted posts older than 7 days.');
    }
}
