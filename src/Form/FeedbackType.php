<?php

namespace App\Form;

use App\Document\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'invalid_message' => 'Veuillez saisir un titre.',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true,
                'invalid_message' => 'Veuillez saisir un contenu.',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'cols' => 33,
                    'placeholder' => 'Votre message',
                    'resize' => 'none'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
