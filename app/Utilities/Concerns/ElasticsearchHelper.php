<?php

namespace App\Utilities\Concerns;

use App\Models\MailMessage;
use Illuminate\Support\Arr;
use App\Utilities\Contracts\ElasticsearchHelperInterface;

class ElasticsearchHelper implements ElasticsearchHelperInterface
{

    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @param  string  $messageBody
     * @param  string  $messageSubject
     * @param  string  $toEmailAddress
     * @return mixed - Return the id of the record inserted into Elasticsearch
     */
    public function storeEmail(mixed $id, string $messageBody, string $messageSubject, string $toEmailAddress): mixed
    {
        try {
            return app('elasticsearch')->index([
                'index' => 'emails',
                'body' => [
                    'id' => $id,
                    'subject' => $messageSubject,
                    'email' => $toEmailAddress,
                    'body' => $messageBody,
                ]
            ])['_id'];
        } catch(\Exception $e) {
            // Log the error
            return false;
        }
    }

    /**
     * Search for emails in Elasticsearch.
     *
     * @param  string  $query
     * @return array
     */
    public function searchEmails(string $query): array
    {
        try {
            $response = app('elasticsearch')->search([
                'index' => 'emails',
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['body', 'subject', 'email']
                        ]
                    ]
                ]
            ]);
            return $this->parseResults($response['hits']['hits'] ?? []);
        } catch(\Exception $e) {
            // Log the error
            return [];
        }
    }

    /**
     * Parse the results from Elasticsearch.
     *
     * @param  array  $results
     * @return array
     */
    private function parseResults(array $results)
    {
        $data = [];
        foreach ($results as $result) {
            $data[] = $result['_source'];
        }

        return $data;
    }
}
