<?php
/**
 * Custom Observer to return result as array
 */
require __DIR__ . '/vendor/autoload.php';

use \Rx\Observable as Observable;
use \Rx\React\Http as Http;

class ArrayObserver {
    private $array;

    function __invoke($value)
    {
        $this->array[] = $value;
    }

    function asArray()
    {
        return $this->array;
    }

}

function getPosts()
{
    $loop = \EventLoop\getLoop();

    $result = new ArrayObserver();
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
        ->map(
            function ($post) {
                return $post->title;
            }
        )
        ->subscribe($result);

    $loop->run();

    return $result->asArray();
}

$posts = getPosts();
var_dump($posts);
