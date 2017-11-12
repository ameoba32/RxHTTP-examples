<?php
/**
 * Using RxPHP inside the function
 */

require __DIR__ . '/vendor/autoload.php';

use \Rx\Observable as Observable;
use \Rx\React\Http as Http;


function ()
{
    $loop = \EventLoop\getLoop();

    $result = [];
    Observable::of('https://jsonplaceholder.typicode.com/posts')
        ->flatMap(
            function ($url) {
                return Http::get($url);
            }
        )
        ->map('json_decode')
        ->flatMap(
            function ($posts) {
                return Observable::fromArray($posts);
            }
        )
        ->filter(
            function ($post) {
                return strlen($post->title) <= 20;
            }
        )
        ->subscribe(
            function ($post) use (&$result) {
                $result[] = $post->title;
            }
        );

    $loop->run();

    return $result;
}

$posts = getPosts();
var_dump($posts);
