## Bit6 Token Generator for PHP

A super simple application demonstrating the external authentication in Bit6.


### Prerequisites

* Get the API Key and Secret at [Bit6 Dashboard](https://dashboard.bit6.com).

### Composer

To incorporate into your current project simply run:
```sh
$ composer.phar require bit6/bit6-token-generator-php
```

To generate token:
```php
// Ideally pull variables by parsing ini file or from env
$apiKey = 'API_KEY';  
$apiSecret = 'API_SECRET';

// Create new TokenGenerator
$bit6_tg = new Bit6\TokenGenerator($apiKey, $apiSecret);

// Get identities from your app post authentication
$identities = "mailto:user@test.com";

// Generate token
$token = $bit6_tg->createToken($identities);
```
Then authenticate user in javascript after loading bit6.min.js by:
```html
<script>var token = '<?= $token ?>';</script>
```
```js
  // Authenticate with external token
  b6.session.external(token, function(err) {
    if (err) {
      console.log('Token login error', err);
    }
    else {
      console.log('Token login successful');
    }
  });
``` 

### Running Locally

```sh
$ git clone git@github.com:bit6/bit6-token-generator-php.git
$ cd bit6-token-generator-php
$ composer update
```

Specify your Bit6 API key and secret using environment variables or a local `.env` config file. The file should contain two lines:

```
BIT6_API_KEY=abc
BIT6_API_SECRET=xyz
```

Start the application

```sh
$ php -S localhost:5000 -t example/
# Alternatively run:
# heroku local
```

Your app should now be running on [localhost:5000](http://localhost:5000/).


### Deploying to Heroku

Make sure you have the [Heroku Toolbelt](https://toolbelt.heroku.com/) installed.

```sh
$ heroku create
$ git push heroku master
```
or

[![Deploy to Heroku](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

Set Bit6 API key and secret:

```sh
$ heroku config:set BIT6_API_KEY=abc
$ heroku config:set BIT6_API_SECRET=xyz
```


### Generating a Token

You would normally generate an external token by doing a POST from your app client to your application server. To simulate this using `curl`:

```sh
curl -X POST \
    -H "Content-Type: application/json" \
    -d '{"identities": ["usr:john","tel:+12123331234"]}' \
    http://localhost:5000/auth.php
```

The response should be a JSON object:

```json
{
    "ext_token": "..."
}
```


### Documentation

For more information about using PHP on Heroku, see these Dev Center articles:

- [Getting Started with PHP on Heroku](https://devcenter.heroku.com/articles/getting-started-with-php)
