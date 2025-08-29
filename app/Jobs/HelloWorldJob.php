<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HelloWorldJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()

    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Hello World mesajını log'a yazalım
        \Log::info('Hello World from Cron Job! Executed at: ' . now()->format('Y-m-d H:i:s'));
        
        // Konsola da yazdıralım (eğer queue worker çalışıyorsa görünür)
        echo "Hello World from Cron Job! Executed at: " . now()->format('Y-m-d H:i:s') . "\n";
    }
}
