<?php

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use HansOtt\PSR7Cookies\SetCookie;
use HansOtt\PSR7Cookies\RequestCookieCollection;

require_once __DIR__.'/../vendor/autoload.php';

date_default_timezone_set('UTC');

$serverRequest = ServerRequest::fromGlobals();
$requestCookies = RequestCookieCollection::createFromRequest($serverRequest);

$counter = 0;
if ($requestCookies->has('counter')) {
    $counterCookie = $requestCookies->get('counter');
    $counter = (int) $counterCookie->getValue();
    $counter++;
}

$setCounterCookie = SetCookie::thatStaysForever('counter', $counter);

$response = new Response();
$body = \GuzzleHttp\Psr7\stream_for(sprintf('Counter: %d', $counter));
$response = $response->withBody($body);
$response = $setCounterCookie->addToResponse($response);

header(sprintf(
    'HTTP/%s %s %s',
    $response->getProtocolVersion(),
    $response->getStatusCode(),
    $response->getReasonPhrase()
));

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}

echo $response->getBody();
