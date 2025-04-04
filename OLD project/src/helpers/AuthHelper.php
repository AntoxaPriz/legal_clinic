<?php
// helpers/AuthHelper.php
namespace src\helpers;
class AuthHelper
{

    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public static function login($user)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    }

    public static function logout()
    {
        session_start();
        session_unset();
        session_destroy();
    }
}
