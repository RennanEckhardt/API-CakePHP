<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Stores extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('stores', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_0900_ai_ci',
        ]);

        $table
            ->addColumn('name', 'string', ['limit' => 200, 'null' => false])
            ->create();
    }
}
