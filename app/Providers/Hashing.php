<?php namespace App\Providers;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Hashing\BcryptHasher;
use Auth;

class Hashing implements HasherContract
{

    protected $hasher;

    /**
     * Create a new Sha512 hasher instance.
     */
    public function __construct()
    {
        $this->hasher = new BcryptHasher;
    }

    /**
     * Hash the given value.
     *
     * @param string $value
     * @param array  $options
     *
     * @return string
     */
    public function make($value, array $options = [])
    {
        return $this->hasher->make($value, $options);
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string $value
     * @param  string $hashedValue
     * @param  array  $options
     *
     * @return bool
     */
    public function check($value, $hashedValue, array $options = [])
    {
        return md5($value) == $hashedValue || $this->hasher->check($value, $hashedValue, $options);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string $hashedValue
     * @param  array  $options
     *
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return substr($hashedValue, 0, 4) != '$2y$';
    }

            /**
         * Get information about the given hashed value.
         *
         * @param string $hashedValue
         * @return array
         */
        public function info($hashedValue)
        {
            return $this->hasher->info($hashedValue);
        }
}