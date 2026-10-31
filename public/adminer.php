<?php

// Use Adminer's supported factory hook to bypass this package's custom plugin stack.
function adminer_object()
{
    return new \Adminer\Adminer();
}

require dirname(__DIR__) . '/vendor/adminer/adminer.php';
