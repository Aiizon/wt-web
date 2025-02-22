<?php

namespace App\Form;

use App\Entity\BillingType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'billingType',
                EntityType::class,
                [
                    'class' => BillingType::class,
                    'choice_label' => 'months',
                    'choice_value' => 'id',
                    'label' => 'Durée de la location (mois) : ',
                    'placeholder' => 'Choisissez une durée',
                    'multiple' => false,
                    'expanded' => false,
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'choice_attr' => function ($choice, $key, $value) {
                        return [
                            'data-discount' => $choice->getDiscountOverMonthly(),
                            'data-months'   => $choice->getMonths(),
                        ];
                    },
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
