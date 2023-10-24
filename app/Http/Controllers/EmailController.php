<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use App\Mail\UserEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\SendEmailRequest;
use App\Utilities\Contracts\RedisHelperInterface;
use App\Utilities\Contracts\ElasticsearchHelperInterface;

class EmailController extends Controller
{
    // TODO: finish implementing send method
    public function send(SendEmailRequest $request)
    {
        /** @var ElasticsearchHelperInterface $elasticsearchHelper */
        $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);

        /** @var RedisHelperInterface $redisHelper */
        $redisHelper = app()->make(RedisHelperInterface::class);

        collect($request->emails)
            ->map(fn ($email) => new Fluent($email))
            ->each(fn ($email) => SendEmail::dispatch($email->toArray(), $request->user()))
            ->each(fn ($email) => $redisHelper->storeRecentMessage(
                $request->user()->id,
                $email->subject,
                $email->email,
                $email->body
            ))
            ->each(fn ($email) => $elasticsearchHelper->storeEmail(
                $request->user()->id,
                $email->body,
                $email->subject,
                $email->email,
            ));

        return response()->json(['message' => 'Emails queued successfully']);
    }

    //  TODO - BONUS: implement list method
    public function list(Request $request)
    {
        if ($request->search) {
            /** @var ElasticsearchHelperInterface $elasticsearchHelper */
            $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);

            $emails = $elasticsearchHelper->searchEmails($request->search, $request->user()->id);

            return response()->json(['emails' => $emails]);
        }
        /** @var RedisHelperInterface $redisHelper */
        $redisHelper = app()->make(RedisHelperInterface::class);

        $recentMessages = $redisHelper->getRecentMessages(auth()->user()->id);

        return response()->json(['emails' => $recentMessages]);
    }
}
