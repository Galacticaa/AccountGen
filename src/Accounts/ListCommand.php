<?php namespace AccountGen\Accounts;

use AccountGen\Account;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    protected $modes= [
        'creation',
        'tutorial',
    ];

    protected function configure()
    {
        $this->setName('accounts:list');
        $this->setDescription('Get a list of accounts from the pool.');

        $this->addArgument('mode', InputArgument::REQUIRED, "Account listing mode, either `creation` or `tutorial`");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mode = $input->getArgument('mode');
        $format = 'formatFor'.($mode == 'creation' ? 'RocketMap' : 'Kinan');

        if (!in_array($mode, $this->modes)) {
            $output->writeLn("Invalid list mode '{$mode}'.");

            exit(1);
        }

        $accounts = $this->getAccounts($input, $output, $mode);

        if ($accounts->isEmpty()) {
            exit(1);
        }

        foreach ($accounts as $account) {
            $output->writeLn($account->$format());
        }
    }

    protected function getAccounts($input, $output, $mode)
    {
        $func = 'get'.ucfirst($mode).'Accounts';

        return $this->$func($input, $output);
    }

    protected function getCreationAccounts($input, $output)
    {
        return Account::whereNull('registered_at')->get();
    }

    protected function getTutorialAccounts($input, $output)
    {
        $output->writeLn("#username;email;password;dob;country");

        return Account::whereNotNull('registered_at')
                      ->whereNotNull('activated_at')
                      ->whereNull('completed_at')->get();
    }
}
