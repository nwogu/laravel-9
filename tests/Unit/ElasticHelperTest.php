<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\MailMessage;
use App\Jobs\SendEmailBatchJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ElasticHelperTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_elastic_helper_can_store_and_retrieve_message()
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

        $elasticHelper = app()->make(\App\Utilities\Contracts\ElasticsearchHelperInterface::class);

        SendEmailBatchJob::dispatch($batch);
        $results = $elasticHelper->searchEmails('Test Body 2');

        $this->assertTrue([
            'body' => $results[0]['body'],
            'subject' => $results[0]['subject'],
            'email' => $results[0]['email'],
        ] === [
            'body' => $data[1]['body'],
            'subject' => $data[1]['subject'],
            'email' => $data[1]['email'],
        ]);
    }
}
