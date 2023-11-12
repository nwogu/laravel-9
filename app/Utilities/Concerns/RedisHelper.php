<?php

namespace App\Utilities\Concerns;

use Illuminate\Support\Facades\Redis;
use App\Utilities\Contracts\RedisHelperInterface;

class RedisHelper implements RedisHelperInterface
{
    private static array $recents = [];

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
        if ($data = Redis::get($id)) {
            return json_decode($data, true);
        }
        return [];
    }

    /**
     * Store the recent messages in Redis.
     * @return void
     */
    public function __destruct()
    {
        //Make use of redis pipeline to avoid multiple round trips to redis
        Redis::pipeline(function ($pipe) {
            foreach (self::$recents as $key => $value) {
                $pipe->del($key);
                $pipe->set($key, json_encode($value));
            }
        });

        self::$recents = [];
    }
}
