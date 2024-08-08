<?php
require_once 'Validator.php';

class UserValidator extends Validator
{
    public static function validateName($name)
    {
        self::isNotEmpty($name, 'Name');
        self::isLessThanOrEqualToMaxLength(100, $name, 'Name');
    }

    public static function validatePhone($phone)
    {
        self::isNotEmpty($phone, 'Phone');

        if (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
            throw new RuntimeException('A valid phone number is required');
        }
    }

    public static function validateEmail($email)
    {
        self::isNotEmpty($email, 'Email');
        self::isEmail($email);
    }

    public static function validatePassword($password)
    {
        self::isNotEmpty($password, 'Password');
        self::isGreaterThanOrEqualToMinLength(12, $password, 'Password');

        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\\d).*$/', $password)) {
            throw new RuntimeException('Password must include at least one uppercase letter, one lowercase letter, and one digit');
        }
    }
}
