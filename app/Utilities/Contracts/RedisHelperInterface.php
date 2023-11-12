<?php

namespace App\Utilities\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RedisHelperInterface {
    /**
     * Store the id of a message along with a message subject in Redis.
     *
     * @param  mixed  $id
     * @param  array $messageCollection
     * @return void
     */
    public function storeRecentMessage(
        mixed $id,
        array $messageCollection
    ): void;

    /**
     * Get the recent messages for a given id in Redis.
     * @param  mixed  $id
     * @return array
     */
    public function getRecentMessages(mixed $id): array;
}
