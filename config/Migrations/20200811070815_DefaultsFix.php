<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class DefaultsFix extends AbstractMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {

        $this->table('orders')
            ->changeColumn('name', 'string', [
                'default' => '',
                'limit' => 191,
                'null' => false,
            ])
            ->update();

        $this->table('paypalmes')
            ->changeColumn('link', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->changeColumn('name', 'string', [
                'default' => '',
                'limit' => 100,
                'null' => false,
            ])
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {

        $this->table('orders')
            ->changeColumn('name', 'string', [
                'default' => '0',
                'length' => 191,
                'null' => false,
            ])
            ->update();

        $this->table('paypalmes')
            ->changeColumn('link', 'string', [
                'default' => '0',
                'length' => 100,
                'null' => false,
            ])
            ->changeColumn('name', 'string', [
                'default' => null,
                'length' => 100,
                'null' => true,
            ])
            ->update();
    }
}
