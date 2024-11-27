<?php

namespace App\Form;

use App\DTO\RentalDto;
use App\Entity\BillingType;
use App\Validator\Constraints\ValidDiscount;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

class RentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom : ',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom : ',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse de facturation : ',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité : ',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 20,
                    'step' => 1,
                    'value' => 1,
                ],
                'constraints' => [
                    new Positive(),
                ],
            ])
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
                        return ['data-discount' => $choice->getDiscountOverMonthly()];
                    },
                ]
            )
            ->add('doRenew', RadioType::class, [
                'label' => 'Renouveler automatiquement ?',
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('discount', TextType::class, [
                'label' => 'Code promotionnel',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new ValidDiscount(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RentalDto::class,
        ]);
    }
}