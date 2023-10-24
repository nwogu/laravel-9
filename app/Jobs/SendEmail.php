<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\UserEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Fluent $email;

    public User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(array $email, User $user)
    {
        $this->email = new Fluent($email);
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email->email)
            ->send(new UserEmail(
                $this->email->subject,
                $this->email->body,
                $this->user->email
            )
        );
        $this->user->touch('last_email_sent_at');
    }
}
