<?php namespace AccountGen\Services;

use Kinan;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServiceCommand extends Command
{
    protected $serviceMap = [
        'ns' => 'Worker',
        'os' => 'Server',
        'mail' => 'Kinan',
    ];

    protected function configure()
    {
        $this->setName('service');
        $this->setDescription('');

        $this->addArgument('action', InputArgument::REQUIRED, 'Action to perform (start, stop or restart)');
        $this->addArgument('service', InputArgument::REQUIRED, 'The service(s) to start/stop/restart (ns, os, mail or all)');
        $this->addOption('instance', 'i', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $service = $input->getArgument('service');

        if (!in_array($action, ['start', 'stop', 'restart'])) {
            $output->writeLn("Invalid action '{$action}'.");
            exit(1);
        }

        if (!array_key_exists($service, $this->serviceMap)) {
            $output->writeLn("Invalid service '{$service}'.");
            exit(1);
        }

        foreach ($this->getClasses($input, $output, $service) as $svc) {
            if ($action == 'restart') {
                $svc->stop();
                sleep(1);
                $svc->start();
            } else {
                $svc->$action();
            }
        }
    }

    protected function getClasses($input, $output, $service)
    {
        if ($service == 'all') {
            return [new Kinan($output), new Server($output)];
        }

        if ($service == 'ns') {
            return [new Worker($output, $this->getOption('instance'))];
        }

        $class = "AccountGen\Services\\{$this->serviceMap[$service]}";

        return [new $class($output)];
    }
}
