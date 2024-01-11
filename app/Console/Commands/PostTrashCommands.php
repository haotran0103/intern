<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
class PostTrashCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:post-trash-commands';

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
        $sevenDaysAgo = now()->subDays(1);
        Post::onlyTrashed()
            ->where('deleted_at', '<', $sevenDaysAgo)
            ->forceDelete(); // Xóa vĩnh viễn khỏi thùng rác
        $this->info('Deleted posts older than 7 days.');
    }
}
