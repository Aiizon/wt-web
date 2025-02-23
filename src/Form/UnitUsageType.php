<?php

namespace App\Form;

use App\Entity\Unit;
use App\Entity\UnitUsage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnitUsageType extends AbstractType
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Unit::class,
        ]);
    }
}
