<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Tests\Traits;

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\ValidatorBuilder;

trait FormValidationTrait
{
    /**
     * @param array<string,string> $formData
     *
     * @throws \Exception
     */
    public function getFormViolations(object $model, string $typeClass, array $formData): ConstraintViolationListInterface
    {
        if (!$this instanceof TypeTestCase) {
            throw new \Exception(sprintf('The trait "FormValidationTrait" can only be added to a class that extends "%s".', TypeTestCase::class));
        }

        $validator = (new ValidatorBuilder())
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        $form = $this->factory->create($typeClass, $model);
        $form->submit($formData);

        return $validator->validate($model);
    }
}
