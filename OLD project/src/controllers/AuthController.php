<?php

namespace src\controllers;

use src\models\User;

require_once 'models/User.php';

class AuthController
{
    private $user;

    public function __construct($pdo)
    {
        $this->user = new User($pdo);
    }

    public function login($username, $password)
    {
        if ($this->user->login($username, $password)) {
            header('Location: /dashboard.php');
            exit();
        } else {
            echo "Неверный логин или пароль.";
        }
    }

    public function register($username, $password)
    {
        if ($this->user->register($username, $password)) {
            echo "Регистрация прошла успешно.";
        } else {
            echo "Ошибка регистрации.";
        }
    }
}

?>
