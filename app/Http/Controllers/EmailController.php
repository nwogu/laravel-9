<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Jobs\SendEmail;
use App\Mail\UserEmail;
use App\Models\MailMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use App\Models\MailMessageBatch;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\SendEmailRequest;
use App\Utilities\Contracts\RedisHelperInterface;
use App\Utilities\Contracts\ElasticsearchHelperInterface;

class EmailController extends Controller
{
    // TODO: finish implementing send method
    public function send(SendEmailRequest $request, User $user)
    {
        $emailData = [];
        $mailMessageBatch = MailMessageBatch::current();
        $userId = $request->user()->id;

        collect($request->emails)
            ->each(function($email) use (&$emailData, $mailMessageBatch, $userId) {
                $emailData[] = [
                'subject' => $email['subject'],
                'body' => $email['body'],
                'email' => $email['email'],
                'user_id' => $userId,
                'mail_message_batch_id' => $mailMessageBatch->id,
                ];
            });

        MailMessage::insert($emailData);
        $mailMessageBatch->process();

        return response()->json(['message' => 'Emails queued successfully']);
    }

    //  TODO - BONUS: implement list method
    public function list(Request $request)
    {
        if ($request->search) {
            /** @var ElasticsearchHelperInterface $elasticsearchHelper */
            $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);

            $emails = $elasticsearchHelper->searchEmails($request->search);

            return response()->json(['emails' => $emails]);
        }
        /** @var RedisHelperInterface $redisHelper */
        $redisHelper = app()->make(RedisHelperInterface::class);

        $recentMessages = $redisHelper->getRecentMessages('emails');

        return response()->json(['emails' => $recentMessages]);
    }
}
