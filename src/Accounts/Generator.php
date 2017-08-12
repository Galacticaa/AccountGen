<?php namespace AccountGen\Accounts;

use Exception;
use AccountGen\Account;
use Faker\Factory;

class Generator
{
    protected $faker;

    protected $instance;

    protected $batch;

    protected $symbols = ['?', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', ']', '<', '>'];

    public function __construct($instance = null, $batch = null)
    {
        $this->faker = Factory::create();

        $this->instance = $instance;
        $this->batch = $batch;
    }

    public function generateOne(string $basename = null)
    {
        $account = new Account($this->randomDomain());
        $account->username = $this->faker->unique()->bothify($basename ?? $this->username());
        $account->password = $this->password();
        $account->birthday = $this->birthday();
        $account->instance = $this->instance;
        $account->batch = $this->batch;

        $account->save();

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

    protected function randomDomain()
    {
        $domains = @include 'config/domains.php';

        if ($domains === false || count($domains) === 0) {
            throw new Exception("No domains defined in config/domains.php");
        }

        return count($domains) === 1 ? $domains[0] : $domains[array_rand($domains)];
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
        $symbols = [];
        while (0 === count(array_intersect($this->symbols, $symbols))) {
            $symbols = $this->faker->randomElements($this->symbols, $this->faker->numberBetween(2, 4));
        }

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
