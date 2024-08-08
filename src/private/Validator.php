<?php
class Validator
{
    protected static function isNotEmpty($field, $field_name)
    {
        if (empty($field)) {
            throw new RuntimeException("$field_name must not be empty");
        }
    }

    protected static function isLessThanOrEqualToMaxLength($length, $field, $field_name)
    {
        if (strlen($field) > $length) {
            throw new RuntimeException("$field_name length must be less than or equal to $length");
        }
    }

    protected static function isGreaterThanOrEqualToMinLength($length, $field, $field_name)
    {
        if (strlen($field) < $length) {
            throw new RuntimeException("$field_name length must be greater than or equal to $length");
        }
    }

    protected static function isEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('A valid email is required');
        }
    }
}
