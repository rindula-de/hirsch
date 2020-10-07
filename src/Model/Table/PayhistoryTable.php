<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Payhistory;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Payhistory Model
 *
 * @property PaypalmesTable&BelongsTo $Paypalmes
 *
 * @method Payhistory newEmptyEntity()
 * @method Payhistory newEntity(array $data, array $options = [])
 * @method Payhistory[] newEntities(array $data, array $options = [])
 * @method Payhistory get($primaryKey, $options = [])
 * @method Payhistory findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Payhistory patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Payhistory[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Payhistory|false save(EntityInterface $entity, $options = [])
 * @method Payhistory saveOrFail(EntityInterface $entity, $options = [])
 * @method Payhistory[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Payhistory[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Payhistory[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Payhistory[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin TimestampBehavior
 */
class PayhistoryTable extends Table
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

        $this->setTable('payhistory');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Paypalmes', [
            'foreignKey' => 'paypalme_id',
            'joinType' => 'INNER',
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
        $rules->add($rules->existsIn(['paypalme_id'], 'Paypalmes'), ['errorField' => 'paypalme_id']);

        return $rules;
    }
}
