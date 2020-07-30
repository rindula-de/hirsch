<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Produkte extends AbstractMigration
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
            ->dropForeignKey([], 'orders_ibfk_2')
            ->dropForeignKey([], 'orders_ibfk_1')
            ->removeIndexByName('paypalme')
            ->update();

        $this->table('orders')
            ->removeColumn('paypalme')
            ->changeColumn('name', 'string', [
                'default' => '0',
                'limit' => 191,
                'null' => false,
            ])
            ->update();
        $this->table('hirsch')
            ->addColumn('slug', 'string', [
                'default' => null,
                'limit' => 191,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addIndex(
                [
                    'slug',
                ],
                ['unique' => true]
            )
            ->create();

        $this->table('orders')
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'FK_orders_hirsch',
                ]
            )
            ->update();

        $this->table('orders')
            ->addForeignKey(
                'name',
                'hirsch',
                'slug',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT',
                ]
            )
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
            ->dropForeignKey(
                'name'
            )->save();

        $this->table('orders')
            ->removeIndexByName('FK_orders_hirsch')
            ->update();

        $this->table('orders')
            ->addColumn('paypalme', 'integer', [
                'after' => 'note',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->changeColumn('name', 'string', [
                'default' => null,
                'length' => 500,
                'null' => false,
            ])
            ->addIndex(
                [
                    'paypalme',
                ],
                [
                    'name' => 'paypalme',
                ]
            )
            ->update();

        $this->table('orders')
            ->addForeignKey(
                'paypalme',
                'paypalmes',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'NO_ACTION',
                ]
            )
            ->addForeignKey(
                'paypalme',
                'paypalmes',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'RESTRICT',
                ]
            )
            ->update();

        $this->table('hirsch')->drop()->save();
    }
}
