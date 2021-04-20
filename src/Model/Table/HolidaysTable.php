<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Holiday;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Holidays Model
 *
 * @method Holiday newEmptyEntity()
 * @method Holiday newEntity(array $data, array $options = [])
 * @method Holiday[] newEntities(array $data, array $options = [])
 * @method Holiday get($primaryKey, $options = [])
 * @method Holiday findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Holiday patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Holiday[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Holiday|false save(EntityInterface $entity, $options = [])
 * @method Holiday saveOrFail(EntityInterface $entity, $options = [])
 * @method Holiday[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Holiday[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Holiday[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Holiday[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class HolidaysTable extends Table
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

        $this->setTable('holidays');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->date('start')
            ->requirePresence('start', 'create')
            ->notEmptyDate('start');

        $validator
            ->date('end')
            ->requirePresence('end', 'create')
            ->notEmptyDate('end');

        return $validator;
    }
}
