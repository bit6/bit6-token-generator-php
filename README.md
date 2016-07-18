# Bit6 Token Generator for PHP [![Build Status](https://travis-ci.org/jwelmac/bit6-token-generator-php.svg?branch=master)](https://travis-ci.org/jwelmac/bit6-token-generator-php)

A PHP package demonstrating the use of delegated authentication in Bit6.


## Prerequisites

* Get the API Key and Secret at [Bit6 Dashboard](https://dashboard.bit6.com).

## Install via Composer

To incorporate into your current project simply run:
```sh
$ composer.phar require bit6/bit6-token-generator-php
```

### Generating Tokens

* Create token generator:

```php
// Ideally pull variables by parsing ini file or from env
$apiKey = 'API_KEY';  
$apiSecret = 'API_SECRET';

// Create new TokenGenerator
$bit6_tg = new Bit6\TokenGenerator($apiKey, $apiSecret);
```

* Get identities from your app following internal authentication.

* Generate token using one of the following options:

*Option 1: Using a string to represent an identity URI*
```php
$identities = "mailto:user@test.com";

// Generate token
$token = $bit6_tg->createToken($identities);
```

*Option 2: Using an indexed array of identity URIs*

```php
$identities = array("usr:john123", "tel:12345678901");

// Generate token
$token = $bit6_tg->createToken($identities);
```
*Option 3: Using an associative array of options*

```php
$options = array(
  "identities" => array("usr:john123", "mailto:user@test.com"),
  "issued" => 1468709885,
  "expires" => 1468796285
);

// Generate token
$token = $bit6_tg->createToken($options);
```

### Create Token Options
The `createToken` method can be called with an associative array with the following keys:
* `identities` (required) - A string or array of strings of identity URIs as shown below.
When an array is used the first value becomes the primary identity.

<table>
  <tr>
    <th>Protocol</th>
    <th>Data (RegEx)</th>
    <th>Type</th>
    <th>Example</th>
  </tr>
  <tr>
    <td>usr</td>
    <td>/^[a-z0-9.]+$/</td>
    <td>User</td>
    <td>usr:john123</td>
  </tr>
  <tr>
    <td>grp</td>
    <td>/[0-9a-zA-Z._]{22}/</td>
    <td>Group ID</td>
    <td>grp:9de82b5b_236d_40f6_b5a2</td>
  </tr>
  <tr>
    <td>mailto</td>
    <td>/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,8}$/</td>
    <td>Email Address</td>
    <td>mailto:test@user.com</td>
  </tr>
  <tr>
    <td>tel</td>
    <td>/^\+[1-9]{1}[0-9]{8,15}$/</td>
    <td>Telephone Number</td>
    <td>tel:12345678901</td>
  </tr>
  <tr>
    <td>uid</td>
    <td>/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/</td>
    <td>Unique ID</td>
    <td>uid:9de82b5b-236d-40f6-b5a2-e16f5d09651d</td>
  </tr>
</table>

* `issued` (optional) - The unix timestamp at which token was generated (default - current system time)
* `expires` (optional) - The unix timestamp at which the token will expire (default - 10 minutes from time of creation)

### Authentication
Pass the token to browser using your preferred method eg. via JSON response,  url  or inline-script.

Authenticate user in javascript (after loading bit6.min.js) as shown below:
```js
  // Authenticate with external token
  b6.session.external(token, function(err) {
    if (err) {
      // Houston we have a problem!
      console.log('Token login error', err);
    }
    else {
      // Code to run post authentication
      console.log('Token login successful');
    }
  });
</script>
```

## Using example code
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
