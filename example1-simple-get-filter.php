<?php
/**
 * Simple fetch, filter and map example
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
    ->filter(function($post) {
        return strlen($post->title) <= 20;
    })
    ->subscribe(function ($post) {
        echo $post->title . "<br/>";
    });

