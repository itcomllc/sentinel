<?php

/*
 * Part of the Sentinel package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Sentinel
 * @version    6.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2022, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\Sentinel\Hashing;

class Sha512Hasher implements HasherInterface
{
    /**
     * Salt Length
     *
     * @var int
     */
    protected $saltLength = 21;
    /**
     * Stretches
     *
     * @var int
     */
    protected $stretches = 20;

    /**
     * {@inheritdoc}
     */
    public function hash(string $value): string
    {
        $salt = $this->createSalt();

        $digest = $value . $salt;
        for ($i = 1; $i <= $this->stretches; $i++) {
            $digest = hash('sha512', $digest);
        }

        return $salt . $digest;
    }

    /**
     * {@inheritdoc}
     */
    public function check(string $value, string $hashedValue): bool
    {

        $salt = substr($hashedValue, 0, $this->saltLength);

        $digest = $value . $salt;
        for ($i = 1; $i <= $this->stretches; $i++) {
            $digest = hash('sha512', $digest);
        }

        return $this->slowEquals($salt . $digest, $hashedValue);
    }

    /**
     * Create a random string for a salt.
     *
     * @return string
     */
    public function createSalt()
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $this->saltLength);
    }

    /**
     * Compares two strings $a and $b in length-constant time.
     *
     * @param string $a
     * @param string $b
     *
     * @return bool
     */
    protected function slowEquals(string $a, string $b): bool
    {
        $diff = strlen($a) ^ strlen($b);

        for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
            $diff |= ord($a[$i]) ^ ord($b[$i]);
        }

        return $diff === 0;
    }
}
