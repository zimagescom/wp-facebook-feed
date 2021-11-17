# Facebook feed
WordPress plugin to get latest latest posts from Facebook profile or page.

## Usage
This plugin does not have settings page or provide anything visible on front-end. So it's basically dumb plugin if you don't use any filters listed below.

Only mandatory filter to use is `facebook-feed/parameters/access_token`.

Get posts by calling function `facebook_feed()->get_posts()`, pass Facebook id as a only argument. It id can be obtained with [this tool](https://findidfb.com/).

### Usage example for displaying a Facebook page feed

1. Go to [developers.facebook.com](https://developers.facebook.com/) and create app for your WordPress site
2. Generate access token by going [Facebook Graph API Explorer](https://developers.facebook.com/tools/explorer/).
2. Debug access token by going [Facebook Access token debugger](https://developers.facebook.com/tools/debug/accesstoken/).

You need to be *administrator* in the page that you want to access. You can no longer get access token to any page but if you are the administrator, you don't need to send your app to review. Only if you need to access to any public page will you need to go through the app review process.

To obtain a client access token, log in to the [**App Space**](https://developers.facebook.com/apps ) and go to **Settings** > **Advanced** > **Security** > **Client Token**. Grant pretty much all privileges.

Select **Get Token** again and select correct page from **Page Access Token**. After that, copy the long "Access token" shown (can be over 200 characters). This is your access_token.

Notice that this access_token will only grant you permission to that page. For every page, you must create separate access_token.

3. Copy **Access Token** and create filter that returns access token to your **functions.php** :

```php
<?php
/**
 * Facebook feed
 */
 add_filter('facebook-feed/parameters/access_token', 'facebook_access_token' );
 function facebook_access_token() {
    return 'your_access_token_from_fb'; // access_token: 'appid|appsecret'
 }
```

4. Get your Facebook page numeric ID from [findidfb.com](https://findidfb.com/). Go to the page you want your Facebook feed to be displayed, for example **front-page.php** and loop the feed and do stuff (it's always good idea to `var_dump` the data to see what's there to use:

```php
$feed = facebook_feed()->get_posts('569702083062696');

foreach ($feed['data'] as $item) {
  if ($item['story']) {
    $message = $item['story'];
  } else {
    $message = $item['message'];
  }
  echo $message;
}
```

### Limiting feed items
```php
// Limit feed to 6 items
add_filter('facebook-feed/parameters/api', 'facebook_limit' );
function facebook_limit($parameters) {
  $parameters['limit'] = 6;
  return $parameters;
}
```

### Add support for likes field

```php
// Add likes support
add_filter('facebook-feed/parameters/fields', 'facebook_likes' );
function facebook_likes($parameters) {
  $parameters[] = 'likes';
  return $parameters;
}
```

## Hooks
All the settings are set with filters, and there is also few filters to change basic functionality and manipulate data before caching.

#### `facebook-feed/parameters/access_token`
You need most likely global access token to get posts from page, it's App ID and secret separated with |. If you are fetching posts from profile, please provide user access token generated with [Graph API Explorer](https://developers.facebook.com/tools/explorer/).
Defaults to empty string.

#### `facebook-feed/posts_transient`
Change name of the transient for posts. Passed arguments are default name and facebook id.
Defaults to `facebook-user-$fbid`.

#### `facebook-feed/parameters/api`
Modify api call parameters just before sending those. Only passed argument is array of default parameters.

Defaults to access_token, locale, since, limit and fields wth values described later on this document.

#### `facebook-feed/posts`
Manipulate or use data before it's cached to transient. Only passed argument is array of posts.

#### `facebook-feed/posts_transient_lifetime`
Change posts cache lifetime. Only passed argument is default lifetime in seconds.
Defaults to 600 (= ten minutes).

## Default call parameters

Default parameters are below, those can be changed with filters named `facebook-feed/parameters/<PARAMETER>`.

```
[
  ["access_token"] => ""
  ["locale"] => "fr_FR"
  ["since"] => "12/10/2021"
  ["limit"] => "10"
  ["fields"] => [
    "id",
    "created_time",
    "type,message",
    "story",
    "full_picture",
    "link"
  ]
]
```