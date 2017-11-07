<?php
/**
 * Parallel fetch example
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
    ->take(5)
    ->flatMap(function ($post) {
        return Http::get(sprintf('https://jsonplaceholder.typicode.com/posts/%d/comments', $post->id));
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

