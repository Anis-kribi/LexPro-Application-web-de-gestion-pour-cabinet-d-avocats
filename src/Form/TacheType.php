<?php

namespace App\Form;

use App\Entity\Dossier;
use App\Entity\Tache;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['data'] && $options['data']->getId() !== null;

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre *',
                'attr'  => ['placeholder' => 'Ex: Rédiger les conclusions, Préparer audience...'],
            ])
            ->add('dossier', EntityType::class, [
                'class'        => Dossier::class,
                'choice_label' => fn(Dossier $d) => $d->getTitre() . ' (' . ($d->getClient()?->getFullName() ?? '?') . ')',
                'label'        => 'Dossier *',
                'placeholder'  => '-- Sélectionner un dossier --',
                'disabled'     => $isEdit,
            ])
            ->add('assigneA', EntityType::class, [
                'class'        => User::class,
                'choice_label' => fn(User $u) => $u->getFullName() ?: $u->getEmail(),
                'label'        => 'Assigné à',
                'required'     => false,
                'placeholder'  => '-- Non assigné --',
            ])
            ->add('priorite', ChoiceType::class, [
                'label'   => 'Priorité',
                'choices' => [
                    'Basse'   => 'Basse',
                    'Normale' => 'Normale',
                    'Haute'   => 'Haute',
                    'Urgente' => 'Urgente',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label'   => 'Statut',
                'choices' => [
                    'À faire'   => 'À faire',
                    'En cours'  => 'En cours',
                    'Terminée'  => 'Terminée',
                    'Annulée'   => 'Annulée',
                ],
            ])
            ->add('dateEcheance', DateType::class, [
                'label'    => "Date d'échéance",
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => ['rows' => 3, 'placeholder' => 'Détails de la tâche...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
