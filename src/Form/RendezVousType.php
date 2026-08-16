<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\RendezVous;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['data'] && $options['data']->getId() !== null;

        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ]
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return $client->getNom() . ' ' . $client->getPrenom();
                },
                'label' => 'Client',
                'disabled' => $isEdit,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Confirmé' => 'Confirmé',
                    'En attente' => 'En attente',
                    'Annulé' => 'Annulé',
                ],
                'label' => 'Statut',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}