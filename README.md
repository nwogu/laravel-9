<p align="center"><a href="https://autoklose.com" target="_blank"><img src="https://app.autoklose.com/images/svg/autoklose-logo-white.svg" width="400"></a></p>

## Instructions
After cloning repository and running composer commands.

### Start dependencies
```vendor/bin/sail up -d``` to start sail

### Generate API Token
```php artisan app:generate-api-token test@sample.com 123456 --create=TestUser``` to generate api token. Copy the output

### Send Emails
```POST api/send?api_token={token generated from previous command}```

```json
{
  "emails": [
    {
      "email": "test1@gmail.com",
      "subject": "Test subject",
      "body": "test body"
    },
    {
      "email": "test2@gmail.com",
      "subject": "Test tubject",
      "body": "test body 2"
    },
    {
      "email": "test3@gmail.com",
      "subject": "Test tubject 3",
      "body": "test body 3"
    }
  ]
}
```

### Fetch Recently sent emails
```GET api/list?api_token={token generated from previous command}```

### Search within recently sent emails
```GET api/list?search=test3&api_token={token generated from previous command}```

## Testing

```vendor/bin/sail artisan test```