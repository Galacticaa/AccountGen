<?php namespace AccountGen\Accounts;

use AccountGen\Account;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    protected $statuses = [
        'incomplete',
    ];

    protected function configure()
    {
        $this->setName('accounts:list');
        $this->setDescription('Get a list of accounts from the pool.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accounts = Account::whereNull('registered_at')->get();

        if ($accounts->isEmpty()) {
            exit(1);
        }

        $output->writeLn("#username;email;password;dob;country");

        foreach ($accounts as $account) {
            $output->writeLn($account->formatForKinan());
        }
    }
}
