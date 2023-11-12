<?php

namespace App\Jobs;

use App\Mail\UserEmail;
use App\Models\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public MailMessage $mailMessage;

    /**
     * Create a new job instance.
     */
    public function __construct(MailMessage $mailMessage)
    {
        $this->mailMessage = $mailMessage;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->mailMessage->email)
                ->send(new UserEmail(
                    $this->mailMessage->subject,
                    $this->mailMessage->body,
                    $this->mailMessage->user->email
                )
            );
            $this->mailMessage->markAsSent();
            $this->mailMessage->indexSentEmail();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
        finally {
            $this->mailMessage->update([
                'processed_at' => now(),
            ]);
        }
    }
}
