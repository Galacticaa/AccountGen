<?php namespace AccountGen\Services;

class Worker
{
    protected $output;

    protected $instance;

    protected $mapdir = '/rmap';

    public function __construct($output, $instance = null)
    {
        $this->output = $output;
        $this->instance = $instance;
    }

    public function start()
    {
        $this->output->write("Starting RocketMap server... ");

        $pids = $this->getPids();

        if (count($pids)) {
            $this->output->writeLn('Already running!');
            return;
        }

        system("cd {$this->mapdir} && tmux new-session -s \"scan_{$instance}\" -d ./runserver.py -cf \"config/{$instance}.ini\"");

        $this->output->writeLn('Done!');
    }

    public function stop()
    {
        $this->output->write("Stopping RocketMap server... ");

        $pids = $this->getPids();

        if (!count($pids)) {
            $this->output->writeLn("Not running!");
            return;
        }

        foreach ($pids as $pid) {
            $this->output->write('Stopping '.$pid.'... ');
            system(sprintf("kill -15 %s", $pid));
        }

        $this->output->writeLn('Done!');
    }

    protected function getPids()
    {
        if ($instance = $this->instance) {
            echo "Setting instance filter... ";
            $instance = ' | grep "'.$instance.'"';
        } else {
            echo "Finding all ns instances... ";
            $instance = ' | grep -v scanner';
        }

        $cmd = "ps axf | grep runserver.py | grep -v grep | grep -v tmux{$instance} | awk '{ print \$1 }'";

        exec($cmd, $pids);

        return $pids;
    }
}
