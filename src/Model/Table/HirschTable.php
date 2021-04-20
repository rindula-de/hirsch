<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Hirsch;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Hirsch Model
 *
 * @method Hirsch newEmptyEntity()
 * @method Hirsch newEntity(array $data, array $options = [])
 * @method Hirsch[] newEntities(array $data, array $options = [])
 * @method Hirsch get($primaryKey, $options = [])
 * @method Hirsch findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Hirsch patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Hirsch[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Hirsch|false save(EntityInterface $entity, $options = [])
 * @method Hirsch saveOrFail(EntityInterface $entity, $options = [])
 * @method Hirsch[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Hirsch[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Hirsch[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Hirsch[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class HirschTable extends Table
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

        $this->setTable('hirsch');
        $this->setDisplayField('name');
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
            ->scalar('slug')
            ->maxLength('slug', 191)
            ->requirePresence('slug', 'create')
            ->notEmptyString('slug')
            ->add('slug', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->boolean('display')
            ->allowEmptyString('display');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['slug']));

        return $rules;
    }
}
