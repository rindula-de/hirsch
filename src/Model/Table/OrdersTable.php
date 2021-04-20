<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Order;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Orders Model
 *
 * @method Order newEmptyEntity()
 * @method Order newEntity(array $data, array $options = [])
 * @method Order[] newEntities(array $data, array $options = [])
 * @method Order get($primaryKey, $options = [])
 * @method Order findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Order patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Order[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Order|false save(EntityInterface $entity, $options = [])
 * @method Order saveOrFail(EntityInterface $entity, $options = [])
 * @method Order[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Order[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Order[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Order[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin TimestampBehavior
 * @property HirschTable Hirsch
 */
class OrdersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('orders');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->hasOne('Hirsch', [
            'foreignKey' => 'slug',
            'bindingKey' => 'name',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 191)
            ->notEmptyString('name');

        $validator
            ->scalar('orderedby')
            ->maxLength('orderedby', 255)
            ->notEmptyString('orderedby');

        $validator
            ->scalar('note')
            ->maxLength('note', 1000)
            ->allowEmptyString('note');

        $validator
            ->date('for')
            ->requirePresence('for', 'create')
            ->notEmptyDate('for');

        return $validator;
    }
}
