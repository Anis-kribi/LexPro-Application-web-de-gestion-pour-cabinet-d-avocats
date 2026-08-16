<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];

        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr'  => ['class' => 'form-control', 'placeholder' => 'Jean'],
                'constraints' => [new NotBlank(['message' => 'Le prénom est obligatoire.'])],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr'  => ['class' => 'form-control', 'placeholder' => 'Dupont'],
                'constraints' => [new NotBlank(['message' => 'Le nom est obligatoire.'])],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr'  => ['class' => 'form-control', 'placeholder' => 'jean.dupont@cabinet.fr'],
                'constraints' => [new NotBlank(['message' => 'L\'email est obligatoire.'])],
            ])
            ->add('telephone', TextType::class, [
                'label'    => 'Téléphone',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'placeholder' => '+33 6 00 00 00 00'],
            ])
            ->add('roles', ChoiceType::class, [
                'label'    => 'Rôle',
                'choices'  => [
                    'Avocat'    => 'ROLE_AVOCAT',
                    'Assistant' => 'ROLE_ASSISTANT',
                ],
                'expanded' => false,
                'multiple' => false,
                'mapped'   => false,
                'data'     => $options['current_role'],
                'attr'     => ['class' => 'form-select'],
                'constraints' => [new NotBlank(['message' => 'Veuillez choisir un rôle.'])],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label'    => $isEdit ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe',
                'mapped'   => false,
                'required' => !$isEdit,
                'attr'     => ['class' => 'form-control', 'placeholder' => $isEdit ? 'Laisser vide pour conserver' : 'Minimum 6 caractères'],
                'constraints' => $isEdit ? [] : [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Length(['min' => 6, 'minMessage' => 'Minimum {{ limit }} caractères.']),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo de profil (JPG, PNG, WEBP - Max 2Mo)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control', 'accept' => 'image/jpeg, image/png, image/webp, image/gif'],
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
            ->add('manager', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%"ROLE_AVOCAT"%')
                        ->orderBy('u.prenom', 'ASC')
                        ->addOrderBy('u.nom', 'ASC');
                },
                'choice_label' => fn(User $u) => $u->getFullName() ?: $u->getEmail(),
                'label' => 'Avocat responsable (obligatoire pour Assistant)',
                'placeholder' => '-- Assigner un avocat --',
                'required' => false, // La validation se fera via le contrôleur
                'attr' => ['class' => 'form-select manager-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => User::class,
            'is_edit'      => false,
            'current_role' => 'ROLE_AVOCAT',
        ]);
    }
}
