<?php

use Phinx\Seed\AbstractSeed;

class InstanceSeeder extends AbstractSeed
{
    public function run()
    {
        $data = [];
        $instances = include 'config/instances.php';

        foreach ($instances as $instance => $config) {
            $data[] = ['name' => $instance];
        }

        $instances = $this->table('instances');
        $instances->truncate();
        $instances->insert($data)->save();
    }
}
