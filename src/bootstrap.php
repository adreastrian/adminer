<?php

// Upstream calls this factory after declaring its plugin interfaces.
function adminer_object()
{
    return new \Adminer\Plugins(require __DIR__ . '/plugins.php');
}

require dirname(__DIR__) . '/vendor/adminer/adminer.php';
