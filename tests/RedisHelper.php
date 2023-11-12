<?php

namespace Tests;

use Illuminate\Support\Facades\Redis;
use App\Utilities\Contracts\RedisHelperInterface;

class RedisHelper implements RedisHelperInterface
{
    private static array $recents = [];

    /**
     * Store the id of a message along with a message subject in Redis.
     *
     * @param  mixed  $id
     * @param array $messageCollection
     * @return void
     */
    public function storeRecentMessage(
        mixed $id,
        array $messageCollection
    ): void
    {
        self::$recents[$id] = $messageCollection;
    }

    /**
     * Get the recent messages for a given id in Redis.
     * @param  mixed  $id
     * @return array
     */
    public function getRecentMessages(mixed $id): array
    {
        return self::$recents[$id] ?? [];
    }
}
