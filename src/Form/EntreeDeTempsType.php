<?php

namespace App\Form;

use App\Entity\Dossier;
use App\Entity\EntreeDeTemps;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntreeDeTempsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['data'] && $options['data']->getId() !== null;

        $builder
            ->add('dossier', EntityType::class, [
                'class'        => Dossier::class,
                'choice_label' => fn(Dossier $d) => $d->getTitre() . ' (' . ($d->getClient()?->getFullName() ?? '?') . ')',
                'label'        => 'Dossier *',
                'placeholder'  => '-- Sélectionner un dossier --',
                'disabled'     => $isEdit,
            ])
            ->add('user', EntityType::class, [
                'class'        => User::class,
                'choice_label' => fn(User $u) => $u->getFullName() ?: $u->getEmail(),
                'label'        => 'Collaborateur *',
                'placeholder'  => '-- Sélectionner --',
            ])
            ->add('date', DateType::class, [
                'label'  => 'Date *',
                'widget' => 'single_text',
            ])
            ->add('heures', NumberType::class, [
                'label' => 'Heures *',
                'scale' => 2,
                'attr'  => [
                    'placeholder' => '1.5',
                    'min'         => 0.25,
                    'max'         => 24,
                    'step'        => '0.25',
                ],
            ])
            ->add('facturable', CheckboxType::class, [
                'label'    => 'Facturable au client',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description du travail effectué',
                'required' => false,
                'attr'     => ['rows' => 3, 'placeholder' => 'Ex: Rédaction de conclusions, Recherche jurisprudence...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntreeDeTemps::class,
        ]);
    }
}
