<?php

namespace App\Models;

use App\Jobs\SendEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Utilities\Contracts\ElasticsearchHelperInterface;

class MailMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'body',
        'mail_message_batch_id',
        'user_id',
        'email',
        'sent_at',
        'processed_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(MailMessageBatch::class, 'mail_message_batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsSent()
    {
        $this->touch('sent_at');
        $this->user->touch('last_email_sent_at');
    }

    public function sendNow()
    {
        SendEmail::dispatchSync($this);
    }

    public function indexSentEmail()
    {
        app(ElasticsearchHelperInterface::class)->storeEmail(
            $this->id,
            $this->body,
            $this->subject,
            $this->email
        );
    }
}
