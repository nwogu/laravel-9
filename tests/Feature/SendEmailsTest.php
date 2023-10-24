<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendEmailsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_emails_are_queued_sucessfully_with_valid_data()
    {
        Queue::fake();

        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $data = [
            [
                'subject' => 'Test Subject',
                'email' => 'testemail@sample.com',
                'body' => 'Test Body',
            ],
            [
                'subject' => 'Test Subject 2',
                'email' => 'testemail2@sample.com',
                'body' => 'Test Body 2',
            ]
        ];

        $response = $this->post('/api/send?api_token='.$token, ['emails' => $data]);
        $redisHelper = $this->app->make(\App\Utilities\Contracts\RedisHelperInterface::class);
        $elasticsearchHelper = $this->app->make(\App\Utilities\Contracts\ElasticsearchHelperInterface::class);

        $this->assertEquals(
            $data,
            $redisHelper->getRecentMessages($user->id)
        );

        $this->assertTrue(
            $elasticsearchHelper->hasIndex($user->id)
        );

        Queue::assertPushed(function (\App\Jobs\SendEmail $job) use ($user) {
            return $job->user->id === $user->id;
        });

        $response->assertStatus(200);
    }

    public function test_emails_are_not_queued_with_invalid_data()
    {
        Queue::fake();

        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $data = [
            [
                'subject' => 'Test Subject',
                'email' => '',
                'body' => 'Test Body',
            ],
        ];

        $response = $this->post('/api/send?api_token='.$token, ['emails' => $data]);

        Queue::assertNotPushed(\App\Jobs\SendEmail::class);
        $response->assertStatus(422);
    }

}
