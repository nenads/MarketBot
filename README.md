# MarketBot

MarketBot is a PHP port of [Chad Remesch's](https://github.com/chadrem) Ruby Google Play scraper, [market_bot](https://github.com/chadrem/market_bot), and includes support for the Amazon Appstore and Windows Phone marketplaces.

It currently supports search and scraping apps.

It is being used in production on [gdgt](http://gdgt.com).

## Currently supported markets

* Amazon App Store
* Google Play
* Windows Phone
* Apple Store

## Dependencies

* [PHPQuery](http://code.google.com/p/phpquery/)

## Examples

These examples assume you have MarketBot already loaded via your autoloader or some other method.

### Amazon Appstore
    $market = new PastFuture\MarketBot\Android\AmazonAppstore;
    $response = $market->search('engadget');
    var_dump($response);
    exit;

    $market = new PastFuture\MarketBot\Android\AmazonAppstore;
    $response = $market->get('B007C9TQNY');
    var_dump($response);
    exit;

### Google Play
    $market = new PastFuture\MarketBot\Android\GooglePlay;
    $response = $market->search('engadget');
    var_dump($response);
    exit;

    $market = new PastFuture\MarketBot\Android\GooglePlay;
    $response = $market->get('com.aol.mobile.engadget', 'apps');
    var_dump($response);
    exit;

### Windows Phone
    $market = new PastFuture\MarketBot\WindowsPhone;
    $response = $market->search('engadget');
    var_dump($response);
    exit;

    $market = new PastFuture\MarketBot\WindowsPhone;
    $response = $market->get('engadget/24541df1-1aa0-e011-986b-78e7d1fa76f8');
    var_dump($response);
    exit;


