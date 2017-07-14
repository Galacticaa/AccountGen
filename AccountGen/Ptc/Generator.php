#!/bin/php
<?php require 'vendor/autoload.php';

use Faker\Factory;

class Account
{
    const EMAIL_DOMAIN = '';

    protected $faker;

    protected $symbols = ['?', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', ']', '<', '>'];

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function generateOne(string $basename = null)
    {
        $username = $this->faker->unique()->bothify($basename ?? $this->username());
        $password = $this->password();
        $birthday = $this->birthday();
        $email = $username.'@'.self::EMAIL_DOMAIN;

        return implode(';', [$username, $email, $password, $birthday, 'GB']);
    }

    public function generateBatch(int $count = 20, string $basename = null)
    {
        $accounts = [];
        $basename = $basename ?? $this->username();

        for ($i = 0; $i < $count; $i++) {
            $accounts[] = $this->generateOne($basename);
        }

        return $accounts;
    }

    public function username()
    {
        $account = '1234567890987654321';

        while (strlen($account) > 15) {
            $parts = [
                $this->faker->domainWord,
                $this->faker->lastName,
                '##?#',
            ];

            $account = implode('', $parts);
        }

        return $account;
    }

    public function password()
    {
        $symbols = $this->faker->randomElements($this->symbols, $this->faker->numberBetween(1, 4));
        $alphanum = strtoupper($this->faker->bothify('???###'));
        $combined = str_pad($alphanum.implode('', $symbols), 12, '?');

        $password = $this->faker->lexify($combined);

        return $this->faker->shuffle($password);
    }

    public function birthday()
    {
        return $this->faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d');
    }
}

$options = getopt('u::m::n::', ['unique::', 'multiples::username::']);

$unique = $options['unique'] ?? $options['u'] ?? 1;
$multiples = $options['multiples'] ?? $options['m'] ?? 15;
$custom = $options['username'] ?? $options['n'] ?? null;
//'someUsername##?#';

echo '#username;email;password;dob;country'.PHP_EOL;

for ($i = 0; $i < $unique; $i++) {
    $accounts = (new Account)->generateBatch($multiples, $i === 0 ? $custom : null);

    foreach ($accounts as $account) {
        echo $account.PHP_EOL;
    }
}

