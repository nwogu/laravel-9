<?php

namespace App\Models;

use App\Jobs\SendEmailBatchJob;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MailMessageBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'size',
    ];

    public function messages()
    {
        return $this->hasMany(MailMessage::class, 'mail_message_batch_id');
    }

    /**
     * Create or return batch instance, based on size of batch.
     */
    public static function current()
    {
        $batch = static::orderBy('created_at', 'desc')
            ->first();

        if ($batch && $batch->messages()->count() < $batch->size) {
            return $batch;
        }

        return static::create([
            'size' => config('mailmessage.batch_size'),
        ]);
    }

    /**
     * Dispatch job to process mail messages in batch
     */
    public function process()
    {
        SendEmailBatchJob::dispatch($this);
    }

    public function cacheSentMails()
    {
        $redisHelper = app()->make(RedisHelperInterface::class);

        $sentMessages = $this->messages()->whereNotNull('sent_at')
            ->whereNotNull('processed_at')->get()->toArray();

        $redisHelper->storeRecentMessage('emails', $sentMessages);
    }
}
