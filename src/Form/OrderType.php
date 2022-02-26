<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Form;

use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderedby', TextType::class, [
                'row_attr' => ['class' => 'input text required'],
                'attr' => ['placeholder' => 'Max Mustermann'],
                'label' => 'Dein Name',
            ])
            ->add('note', TextType::class, [
                'row_attr' => ['class' => 'input text'],
                'attr' => ['placeholder' => 'Keine', 'list' => 'wishlist', 'autofill' => 'off', 'autocomplete' => 'off'],
                'required' => false,
                'label' => 'Sonderwünsche',
                'help' => 'Extrawünsche, wie "extra Pommes" oder "ohne Salat"',
                'empty_data' => '',
            ])
            ->add('submit', SubmitType::class, [
                'row_attr' => ['class' => 'submit'],
                'attr' => ['class' => 'btn waves-purple waves-effect'],
                'label' => 'Verbindlich bestellen',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
            'for_date' => null,
        ]);
    }
}
