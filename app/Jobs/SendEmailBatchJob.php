<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\MailMessageBatch;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmailBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public MailMessageBatch $batch;

    /**
     * Create a new job instance.
     */
    public function __construct(MailMessageBatch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $messages = $this->batch->messages()->with('user')
            ->whereNull('sent_at')
            ->get();

        foreach ($messages as $message) {
            $message->sendNow();
        }

        $this->batch->cacheSentMails();
    }
}
