<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Paypalme;
use Cake\I18n\Time;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Paypalmes Model
 *
 * @method Paypalme newEmptyEntity()
 * @method Paypalme newEntity(array $data, array $options = [])
 * @method Paypalme[] newEntities(array $data, array $options = [])
 * @method Paypalme get($primaryKey, $options = [])
 * @method Paypalme findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Paypalme patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Paypalme[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Paypalme|false save(EntityInterface $entity, $options = [])
 * @method Paypalme saveOrFail(EntityInterface $entity, $options = [])
 * @method Paypalme[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Paypalme[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Paypalme[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Paypalme[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class PaypalmesTable extends Table
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

        $this->setTable('paypalmes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Payhistory', [
            'foreignKey' => 'paypalme_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('link')
            ->maxLength('link', 100)
            ->notEmptyString('link');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->notEmptyString('name');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        return $validator;
    }

    /**
     * @return null|Paypalme
     */
    public function findActivePayer()
    {

        $active = $this->Payhistory->find()->select([
            'cnt' => 'COUNT(*)',
            'paypalme_id',
        ])->where([
            'created >' => (new Time())->startOfDay(),
        ])->group(['paypalme_id'])->max('cnt');

        if ($active) {
            return $this->get($active->paypalme_id);
        }

        return null;
    }
}
