<?php

namespace App\Form;

use App\Entity\Emprunteur;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices'  => User::ROLES,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('enabled')
            ->add('emprunteur', EntityType::class, [
                'class' => Emprunteur::class,
                'choice_label' => function(Emprunteur $emprunteur) {
                    return "{$emprunteur->getNom()} {$emprunteur->getPrenom()}";
                },
                'multiple' => false,
                'expanded' => false,
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            if (!$user) {
                return;
            }

            if (!$user->getId()) {
                $form->add('password', PasswordType::class);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
