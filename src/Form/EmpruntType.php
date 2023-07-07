<?php

namespace App\Form;

use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateEmprunt')
            ->add('dateRetour')
            ->add('emprunteur', EntityType::class, [
                'class' => Emprunteur::class,
                'choice_label' => function(Emprunteur $emprunteur) {
                    return "{$emprunteur->getNom()} {$emprunteur->getPrenom()} ({$emprunteur->getId()})";
                },
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('livre', EntityType::class, [
                'class' => Livre::class,
                'choice_label' => function(Livre $livre) {
                    $anneeEdition = $livre->getAnneeEdition() ?? '?';
                    return "{$livre->getTitre()} ({$anneeEdition})";
                },
                'multiple' => false,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emprunt::class,
        ]);
    }
}
