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
        $this->output->write("Starting RocketMap instance... ");

        $pids = $this->getPids();

        if (count($pids)) {
            $this->output->writeLn('Already running!');
            return;
        }

        $i = strtolower($this->instance);
        system("cd {$this->mapdir} && tmux new-session -s \"scan_{$i}\" -d ./runserver.py -cf \"config/{$i}.ini\"");

        $this->output->writeLn('Done!');
    }

    public function stop()
    {
        $this->output->write("Stopping RocketMap instance... ");

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
            $instance = ' | grep "'.$instance.'"';
        }

        $cmd = "ps axf | grep 'runserver.py \-cf' | grep -v grep | grep -v tmux{$instance} | awk '{ print \$1 }'";

        exec($cmd, $pids);

        return $pids;
    }
}
