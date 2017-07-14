<?php namespace AccountGen\Ptc;

use Faker\Factory;

class Generator
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
        $account = (new Account(self::EMAIL_DOMAIN))
            ->setUsername($this->faker->unique()->bothify($basename ?? $this->username()))
            ->setPassword($this->password())
            ->setBirthday($this->birthday());

        return $account;
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
