<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Message; // Add this line to import the Message class

class DeleteOldMessages extends Command
{

    protected $name = 'DeleteOldMessages';

    public function handle()
    {
        $sixDaysAgo = Carbon::now()->subDays(6);
        Message::where('created_at', '<', $sixDaysAgo)->delete(); // Use the Message class from the correct namespace
    }
}
