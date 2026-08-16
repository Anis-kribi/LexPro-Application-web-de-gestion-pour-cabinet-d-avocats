<?php

namespace App\Form;

use App\Entity\ArticleFacture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description de la prestation',
                'attr'  => [
                    'rows'        => 2,
                    'placeholder' => 'Ex: Consultation juridique, rédaction de contrat...',
                    'class'       => 'form-control',
                ],
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Qté',
                'scale' => 2,
                'attr'  => [
                    'placeholder' => '1',
                    'class'       => 'form-control article-qte',
                    'min'         => 0.01,
                    'step'        => '0.5',
                ],
            ])
            ->add('prixUnitaire', NumberType::class, [
                'label' => 'Prix unitaire (€ HT)',
                'scale' => 2,
                'attr'  => [
                    'placeholder' => '0.00',
                    'class'       => 'form-control article-prix',
                    'min'         => 0,
                    'step'        => '0.01',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleFacture::class,
        ]);
    }
}
