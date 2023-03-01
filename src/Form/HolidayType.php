<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Form;

use App\Entity\Holidays;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HolidayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateType::class, [
                'row_attr' => ['class' => 'input date'],
                'label' => 'Start',
                'widget' => 'single_text',
            ])
            ->add('end', DateType::class, [
                'row_attr' => ['class' => 'input date'],
                'label' => 'Ende',
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Holidays::class,
        ]);
    }
}
