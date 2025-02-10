<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

function cpuUsage(int $iter): string {
    $start = microtime(true);
    $res  = [];
    $num  = [1723, 4856, 9021, 3478, 6215, 7384, 2549, 8102, 4967, 3690];
    for ($i = 0; $i < $iter; $i++) {
        $idx = $i % count($num);
        sqrt($num[$idx]);
    }

    return $iter.' iterations took '.microtime(true) - $start.' seconds';
}

function cpuHash(int $iter, string $algo = 'sha512'): string {
    // Arbitrary key and salt; you can modify these as needed.
    $key  = 'ABCDEFG12345678ABCDEFG12345678ABCDEFG12345678ABCDEFG12345678';
    $salt = 'HIJKLMN87654321HIJKLMN87654321HIJKLMN87654321HIJKLMN87654321';
    $start = microtime(true);
    hash_pbkdf2($algo, $key, $salt, $iter, 64);
    return 'took '.microtime(true) - $start.' seconds';
}

function memUsage(int $mb, int $sleep): void {
    $allocated= [];
    for ($i = 0; $i < $mb; $i++) {
        $allocated[] = str_repeat('A', 1024 * 1024);
    }
    defer(function(){
      sleep($sleep);
      unset($allocated);
    });
}

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cpu', function (Request $request) {
  $iter = (int) $request->query('iter', 1000000);
  $method = (string) $request->query('method', 'cpuHash');
  $res = $method($iter);
  return response()->json(['message' => 'CPU', 'res' => $res]);
});

Route::get('/mem', function (Request $request) {
  $mb = (int) $request->query('mb', 50);
  $sleep = (int) $request->query('sleep', 2);
  memUsage($mb, $sleep);

  return response()->json(['message' => "Memory"]);
});
