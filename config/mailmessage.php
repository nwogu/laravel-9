<?php

return  [
    /*
    |--------------------------------------------------------------------------
    | Mail Message Configuration
    |--------------------------------------------------------------------------
    |
    | This file configurations related to mail message model and batches.
    |
    */

    'batch_size' => env('MAIL_MESSAGE_BATCH_SIZE', 100),
];