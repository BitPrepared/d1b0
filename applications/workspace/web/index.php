<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../src/App.php';

$app->run();


// @SERVER:
//php -S 127.0.0.1:8080 -t web/
// @TEST:  http://127.0.0.1:8080/api/v1/status



// $blogPosts = [];
// $app->get('/sample/{id}', function ($id) use ($blogPosts) {
//
//     $app['db']; //attivo il facade R
//     $e = R::findAll('table', ' ORDER BY date DESC LIMIT 2');
//
//     // ...
//     // per ricordarsi di validare anche lo User-Agent in caso
//     // ...
//     if (!isset($blogPosts[$id])) {
//         $app->abort(404, "Post $id does not exist.");
//     }
// })
// ->assert('id', '\d+')
// ->when("request.headers.get('User-Agent') matches '/firefox/i'");
