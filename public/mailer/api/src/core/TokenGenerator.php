<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 2:03 PM
 */

namespace core;


class TokenGenerator
{
    /**
     * @return string
     */
    public static function generate()
    {
        return self::generate32();
    }

    /**
     * @return string length 32 char
     */
    public static function generate32()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * @return string length 64 char
     */
    public static function generate64()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    /**
     * @return string length 128 char
     */
    public static function generate128()
    {
        return bin2hex(openssl_random_pseudo_bytes(64));
    }
}