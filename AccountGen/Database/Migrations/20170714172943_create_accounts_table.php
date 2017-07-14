<?php

use Phinx\Migration\AbstractMigration;

class CreateAccountsTable extends AbstractMigration
{
    public function change()
    {
        $accounts = $this->table('accounts', [
            'id' => false,
            'primary_key' => 'username'
        ]);

        $accounts->addColumn('username', 'string', ['limit' => 50, 'default' => ''])
                 ->addColumn('password', 'string', ['limit' => 50, 'default' => ''])
                 ->addColumn('email', 'string', ['default' => ''])
                 ->addColumn('birthday', 'date', ['default' => '1984-08-14'])
                 ->addColumn('country', 'string', ['limit' => 2, 'default' => 'GB'])
                 ->addTimestamps()
                 ->addColumn('registered_at', 'datetime', ['null' => true])
                 ->addColumn('activated_at', 'datetime', ['null' => true])
                 ->addColumn('completed_at', 'datetime', ['null' => true]);

        $accounts->create();
    }
}
