<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\Dossier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du document *',
                'attr'  => ['placeholder' => 'Ex: Contrat de mandat, Jugement du 12/03/2026...'],
            ])
            ->add('dossier', EntityType::class, [
                'class'        => Dossier::class,
                'choice_label' => fn(Dossier $d) => $d->getTitre() . ' (' . ($d->getClient()?->getFullName() ?? '?') . ')',
                'label'        => 'Dossier associé *',
                'placeholder'  => '-- Sélectionner un dossier --',
                'disabled'     => $isEdit,
            ])
            ->add('type', ChoiceType::class, [
                'label'   => 'Type de document',
                'choices' => [
                    'Contrat'        => 'Contrat',
                    'Jugement'       => 'Jugement',
                    'Plainte'        => 'Plainte',
                    'Procuration'    => 'Procuration',
                    'Justificatif'   => 'Justificatif',
                    'Correspondance' => 'Correspondance',
                    'Autre'          => 'Autre',
                ],
            ])
            ->add('confidentialite', ChoiceType::class, [
                'label'   => 'Confidentialité',
                'choices' => [
                    'Public'       => 'Public',
                    'Interne'      => 'Interne',
                    'Confidentiel' => 'Confidentiel',
                    'Secret'       => 'Secret',
                ],
            ])
            ->add('file', FileType::class, [
                'label'    => $isEdit ? 'Remplacer le fichier (optionnel)' : 'Fichier *',
                'mapped'   => false,
                'required' => !$isEdit,
                'constraints' => $isEdit ? [] : [
                    new File([
                        'maxSize'          => '20M',
                        'mimeTypes'        => [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Format autorisé : PDF, Word, Excel, image ou texte (max 20Mo).',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
            'is_edit'    => false,
        ]);
    }
}