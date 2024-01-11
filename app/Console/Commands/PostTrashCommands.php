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
    $sevenDaysAgo = now()->subDays(7); 
    $postsToDelete = Post::onlyTrashed()
        ->where('deleted_at', '<', $sevenDaysAgo)
        ->get();

    foreach ($postsToDelete as $post) {

        if ($post->images) {
            $oldImagePath = public_path($post->images);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $oldFiles = $post->file;
            if ($oldFiles) {
                $oldFilesArray = explode(',', $oldFiles);
                foreach ($oldFilesArray as $fileToDelete) {
                    $filePath = public_path($fileToDelete);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
        }

        $post->forceDelete();
        $this->info('Deleted post ID: ' . $post->id . ' and associated images/files.');
    }
    }
}
