<?php
/**
 * Pass another value connected to emitted event
 */

require __DIR__ . '/vendor/autoload.php';

use \Rx\Observable as Observable;
use \Rx\React\Http as Http;

Observable::fromArray(
    [
        [
            'id' => 1,
            'url' => 'https://jsonplaceholder.typicode.com/posts/1/comments',
        ],
        [
            'id' => 2,
            'url' => 'https://jsonplaceholder.typicode.com/posts/2/comments',
        ],
        [
            'id' => 3,
            'url' => 'https://jsonplaceholder.typicode.com/posts/3/comments',
        ],
    ]
)
    ->flatMap(
        function ($item) {
            return Http::get($item['url'])->combineLatest([Observable::of($item['id'])]);
        }
    )
    ->subscribe(
        function ($res) {
            list($response, $id) = $res;
            echo $id . ' ' . $response .  '<br/>';
        }
    );
