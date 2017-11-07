<?php
/**
 * Parallel fetch with connection limiter
 */
require __DIR__ . '/vendor/autoload.php';

use \Rx\Observable as Observable;
use \Rx\React\Http as Http;

Observable::of('https://jsonplaceholder.typicode.com/posts')
    ->flatMap(function ($url) {
        return Http::get($url);
    })
    ->map('json_decode')
    ->flatMap(function($posts) {
        return Observable::fromArray($posts);
    })
    ->map(function ($post) {
        return Http::get(sprintf('https://jsonplaceholder.typicode.com/posts/%d/comments', $post->id));
    })
    ->groupBy(function () {
        static $index = 0;return $index++ % 5;
    })
    ->flatMap(function (Observable\GroupedObservable $go) {
        return $go->concatAll();
    })
    ->map('json_decode')
    ->flatMap(function($comments) {
        return Observable::fromArray($comments);
    })
    ->map(function($comment) {
        return $comment->email;
    })
    ->distinct()
    ->subscribe(function($email) {
        echo $email . "<br/>\n";
    });
