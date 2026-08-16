<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityRepository;

class ClientType extends AbstractType
{
    public function __construct(private readonly Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label'   => 'Type de client',
                'choices' => [
                    'Particulier' => 'particulier',
                    'Entreprise'  => 'entreprise',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr'     => ['class' => 'form-type-radio'],
            ])
            ->add('nom', TextType::class, [
                'label'    => 'Nom *',
                'attr'     => ['placeholder' => 'Nom de famille'],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('prenom', TextType::class, [
                'label'    => 'Prénom',
                'required' => false,
                'attr'     => ['placeholder' => 'Prénom (particulier)'],
            ])
            ->add('nomEntreprise', TextType::class, [
                'label'    => "Nom de l'entreprise",
                'required' => false,
                'attr'     => ['placeholder' => 'Raison sociale'],
            ])
            ->add('taxId', TextType::class, [
                'label'    => 'Numéro fiscal / Tax ID',
                'required' => false,
                'attr'     => ['placeholder' => 'Ex: FR12345678901'],
            ])
            ->add('email', EmailType::class, [
                'label'    => 'Email',
                'required' => false,
                'attr'     => ['placeholder' => 'client@exemple.com'],
            ])
            ->add('telephone', TextType::class, [
                'label'    => 'Téléphone',
                'required' => false,
                'attr'     => ['placeholder' => '+33 6 12 34 56 78'],
            ])
            ->add('adresse', TextType::class, [
                'label'    => 'Adresse',
                'required' => false,
                'attr'     => ['placeholder' => 'N° rue, nom de rue'],
            ])
            ->add('ville', TextType::class, [
                'label'    => 'Ville',
                'required' => false,
                'attr'     => ['placeholder' => 'Paris'],
            ])
            ->add('statuts', ChoiceType::class, [
                'label'   => 'Statut',
                'choices' => [
                    'Actif'    => 'Actif',
                    'Inactif'  => 'Inactif',
                    'Prospect' => 'Prospect',
                ],
            ])
            ->add('remarques', TextareaType::class, [
                'label'    => 'Remarques internes',
                'required' => false,
                'attr'     => ['rows' => 3, 'placeholder' => 'Notes internes sur ce client...'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo de profil (JPG, PNG, WEBP - Max 2Mo)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control', 'accept' => 'image/*'],
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, WEBP)',
                    ])
                ],
            ])
        ;

        // Si l'utilisateur est un Admin, il peut choisir l'avocat du client
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('avocat', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%"ROLE_AVOCAT"%')
                        ->orderBy('u.prenom', 'ASC')
                        ->addOrderBy('u.nom', 'ASC');
                },
                'choice_label' => fn(User $u) => $u->getFullName() ?: $u->getEmail(),
                'label' => 'Avocat responsable *',
                'placeholder' => '-- Assigner un avocat --',
                'required' => true, // L'admin doit assigner un avocat
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
