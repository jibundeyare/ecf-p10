<?php

namespace App\Form;

use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $year = (int) date('Y');

        $builder
            ->add('dateEmprunt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateRetour', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('emprunteur', EntityType::class, [
                'class' => Emprunteur::class,
                'choice_label' => function(Emprunteur $emprunteur) {
                    return "{$emprunteur->getNom()} {$emprunteur->getPrenom()} ({$emprunteur->getId()})";
                },
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('livre', EntityType::class, [
                'class' => Livre::class,
                'choice_label' => function(Livre $livre) {
                    $anneeEdition = $livre->getAnneeEdition() ?? '?';
                    return "{$livre->getTitre()} ({$anneeEdition})";
                },
                'multiple' => false,
                'expanded' => false,
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
