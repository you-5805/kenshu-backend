<?php

namespace App\lib;

/**
 * グローバル定数から Request オブジェクトを作成する
 */
class Request
{
    public readonly array $server;
    public readonly ?array $get;
    public readonly ?array $post;
    public readonly ?array $files;
    public readonly ?array $session;
    public readonly ?array $request;

    public readonly string $method;
    public readonly string $path;

    public function __construct(
    ) {
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->session = $_SESSION;
        $this->request = $_REQUEST;

        $this->method = $this->server['REQUEST_METHOD'];
        $this->path = $this->server['REQUEST_URI'];
    }

    public function getSession(string $key)
    {
        return $_SESSION[$key];
    }

    public function setSession(string $key, string $value)
    {
        $_SESSION[$key] = $value;
    }

    public function unsetSession(string $key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * check if the $_SESSION has 'user_id'
     */
    public function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * call header for 'Location' to the path. default status_code is 302
     */
    public function redirect(string $path, int $status_code = 302)
    {
        return header('Location: ' . $_ENV['APP_URL'] . $path, true, $status_code);
    }
}
