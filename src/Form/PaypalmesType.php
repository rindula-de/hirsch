<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Form;

use App\Entity\Paypalmes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaypalmesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('link', null, [
                'label' => 'Paypal.me Link',
                'row_attr' => ['class' => 'input text required'],
                'help' => 'Trage hier deinen paypal.me Link ein, ohne Slash am Ende',
                'attr' => ['placeholder' => 'https://paypal.me/rindulalp', 'class' => 'on-surface-text'],
            ])
            ->add('name', null, [
                'label' => 'Dein Name',
                'row_attr' => ['class' => 'input text required'],
                'help' => 'Der Name, der in der Liste angezeigt wird.',
                'attr' => ['placeholder' => 'Max Mustermann', 'class' => 'on-surface-text'],
            ])
            ->add('email', null, [
                'label' => 'Deine E-Mail',
                'row_attr' => ['class' => 'input email'],
                'help' => 'Die E-Mail, an die eine Bestellzusammenfassung geschickt wird. Trägst du hier keine E-Mail ein, musst du unter Bestellungen nachschauen, was bestellt wurde.',
                'attr' => ['placeholder' => 'example@hochwarth-it.de', 'class' => 'on-surface-text'],
            ])
            // add submit button
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
                'attr' => ['class' => 'btn primary on-primary-text'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paypalmes::class,
        ]);
    }
}
