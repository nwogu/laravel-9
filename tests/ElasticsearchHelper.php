<?php

namespace Tests;

use App\Utilities\Contracts\ElasticsearchHelperInterface;

class ElasticsearchHelper implements ElasticsearchHelperInterface
{
    private static array $indices = [];
    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @param  mixed  $id We want to scope the search to the user who sends the message, to avoid data leak
     * @param  string  $messageBody
     * @param  string  $messageSubject
     * @param  string  $toEmailAddress
     * @return mixed - Return the id of the record inserted into Elasticsearch
     */
    public function storeEmail(mixed $id, string $messageBody, string $messageSubject, string $toEmailAddress): mixed
    {
        try {
            self::$indices[$id][] = [
                'subject' => $messageSubject,
                'email' => $toEmailAddress,
                'body' => $messageBody,
            ];
            return true;
        } catch(\Exception $e) {
            // Log the error
            return false;
        }
    }

    /**
     * Search for emails in Elasticsearch.
     *
     * @param  string  $query
     * @param  mixed  $id We want to scope the search to the user who sends the message, to avoid data leak
     * @return array
     */
    public function searchEmails(string $query, mixed $id): array
    {
        try {
            return collect(self::$indices[$id] ?? [])
                ->filter(function ($email) use ($query) {
                    return str_contains($email['subject'], $query)
                        || str_contains($email['email'], $query)
                        || str_contains($email['body'], $query);
                })
                ->toArray();
        } catch(\Exception $e) {
            // Log the error
            return [];
        }
    }

    public function hasIndex(mixed $id): bool
    {
        return isset(self::$indices[$id]);
    }
}
