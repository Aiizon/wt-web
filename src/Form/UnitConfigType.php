<?php

namespace App\Form;

use App\Entity\Unit;
use App\Entity\UnitUsage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnitConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('unitUsage', EntityType::class, [
                'class' => UnitUsage::class,
                'required' => false,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => 'Utilisation',
                'placeholder' => 'Choisir une utilisation',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('isStarted', CheckboxType::class, [
                'required' => false,
                'label' => 'Unité allumée ?',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('status', ChoiceType::class, [
                'required' => true,
                'label' => 'Status',
                'choices' => [
                    'OK'          => Unit::$OK,
                    'KO'          => Unit::$KO,
                    'Maintenance' => Unit::$MAINTENANCE
                ],
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Unit::class,
        ]);
    }
}
