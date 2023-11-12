<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\MailMessage;
use App\Jobs\SendEmailBatchJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RedisHelperTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_redis_helper_can_store_and_retreive_recent_message()
    {
        $batch = \App\Models\MailMessageBatch::current();
        $user = \App\Models\User::factory()->create();
        Mail::fake();

        $data = [
            [
                'subject' => 'Test Subject',
                'email' => 'testemail@sample.com',
                'body' => 'Test Body',
                'mail_message_batch_id' => $batch->id,
                'user_id' => $user->id,
            ],
            [
                'subject' => 'Test Subject 2',
                'email' => 'testemail2@sample.com',
                'body' => 'Test Body 2',
                'mail_message_batch_id' => $batch->id,
                'user_id' => $user->id,
            ]
        ];
        MailMessage::insert($data);

        $redisHelper = app()->make(\App\Utilities\Contracts\RedisHelperInterface::class);

        SendEmailBatchJob::dispatch($batch);

        $this->assertTrue(count($redisHelper->getRecentMessages('emails')) === count($data));
    }
}
