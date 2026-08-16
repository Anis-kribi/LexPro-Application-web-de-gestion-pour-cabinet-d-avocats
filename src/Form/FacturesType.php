<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Dossier;
use App\Entity\Factures;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['data'] && $options['data']->getId() !== null;

        $builder
            ->add('numeroFacture', TextType::class, [
                'label' => 'Numéro de facture *',
                'attr'  => ['placeholder' => 'Ex: FAC-2026-001'],
            ])
            ->add('client', EntityType::class, [
                'class'        => Client::class,
                'choice_label' => fn(Client $c) => $c->getFullName(),
                'label'        => 'Client *',
                'placeholder'  => '-- Sélectionner un client --',
                'disabled'     => $isEdit,
            ])
            ->add('dossier', EntityType::class, [
                'class'        => Dossier::class,
                'choice_label' => fn(Dossier $d) => $d->getTitre() . ' (' . ($d->getClient()?->getFullName() ?? '?') . ')',
                'label'        => 'Dossier associé',
                'required'     => false,
                'placeholder'  => '-- Aucun dossier --',
                'disabled'     => $isEdit,
            ])
            ->add('dateEmission', DateType::class, [
                'label'  => "Date d'émission *",
                'widget' => 'single_text',
            ])
            ->add('dateEcheance', DateType::class, [
                'label'    => "Date d'échéance",
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('tva', NumberType::class, [
                'label' => 'TVA (%)',
                'scale' => 2,
                'attr'  => [
                    'placeholder' => '20',
                    'id'          => 'facture_tva',
                    'min'         => 0,
                    'max'         => 100,
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label'   => 'Statut',
                'choices' => [
                    'Brouillon'   => 'Brouillon',
                    'En attente'  => 'En attente',
                    'Payée'       => 'Payée',
                    'Impayée'     => 'Impayée',
                    'Annulée'     => 'Annulée',
                ],
            ])
            // Articles de facturation (CollectionType inline)
            ->add('articles', CollectionType::class, [
                'entry_type'    => ArticleFactureType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'label'         => false,
                'attr'          => ['class' => 'articles-collection'],
                'entry_options' => ['label' => false],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Factures::class,
        ]);
    }
}