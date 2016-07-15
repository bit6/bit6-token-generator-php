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
<script>
  var token = '<?= $token ?>';

  // Authenticate with external token
  b6.session.external(token, function(err) {
    if (err) {
      console.log('Token login error', err);
    }
    else {
      console.log('Token login successful');
    }
  });
</script>
```
### Create Token
The createToken method take three variables
* $identities (required) - A string or array of strings of identity URIs as shown below
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;margin:0px auto;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-amwm{font-weight:bold;text-align:center;vertical-align:top}
.tg .tg-yw4l{vertical-align:top}
</style>
<table class="tg">
  <tr>
    <th class="tg-amwm">Protocol</th>
    <th class="tg-amwm">Data (RegEx)</th>
    <th class="tg-amwm">Type</th>
    <th class="tg-amwm">Example</th>
  </tr>
  <tr>
    <td class="tg-yw4l">usr</td>
    <td class="tg-yw4l">/^[a-z0-9.]+$/</td>
    <td class="tg-yw4l">User</td>
    <td class="tg-yw4l">usr:john123</td>
  </tr>
  <tr>
    <td class="tg-yw4l">grp</td>
    <td class="tg-yw4l">/[0-9a-zA-Z._]{22}/</td>
    <td class="tg-yw4l">Group</td>
    <td class="tg-yw4l">grp:9de82b5b_236d_40f6_b5a2</td>
  </tr>
  <tr>
    <td class="tg-yw4l">mailto</td>
    <td class="tg-yw4l">/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,8}$/</td>
    <td class="tg-yw4l">Email Address</td>
    <td class="tg-yw4l">mailto:test@user.com</td>
  </tr>
  <tr>
    <td class="tg-yw4l">tel</td>
    <td class="tg-yw4l">/^\+[1-9]{1}[0-9]{8,15}$/</td>
    <td class="tg-yw4l">Telephone Number</td>
    <td class="tg-yw4l">tel:12345678901</td>
  </tr>
  <tr>
    <td class="tg-yw4l">uid</td>
    <td class="tg-yw4l">/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/</td>
    <td class="tg-yw4l">Unique ID</td>
    <td class="tg-yw4l">uid:9de82b5b-236d-40f6-b5a2-e16f5d09651d</td>
  </tr>
</table>

* $ttl (optional) - The length of time before token expires in minutes (default - 10)
* $issued (optional) - The unix timestamp at which token was generated (default - current system time)

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
