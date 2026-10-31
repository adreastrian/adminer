<?php

class AutoLogin extends \Adminer\Plugin
{
    private $env = [
        'USERNAME' => '',
        'PASSWORD' => '',
    ];

    public function __construct()
    {
        $path = dirname(__DIR__, 2) . '/.env';
        if (!is_file($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $entry = explode('=', trim($line), 2);
            if (count($entry) === 2 && array_key_exists($entry[0], $this->env)) {
                $this->env[$entry[0]] = $entry[1];
            }
        }
    }

    public function credentials()
    {
        $username = ($_GET['username'] ?? '') ?: $this->env['USERNAME'];
        $password = Adminer\get_password() ?: $this->env['PASSWORD'];

        return [Adminer\SERVER, $username, $password];
    }

    public function login($login, $password)
    {
        return true;
    }
}
