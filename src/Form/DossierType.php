<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Dossier;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityRepository;

class DossierType extends AbstractType
{
    public function __construct(private readonly Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['data'] && $options['data']->getId() !== null;

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du dossier *',
                'attr'  => ['placeholder' => 'Ex: Divorce Dupont / Succession Martin'],
            ])
            ->add('numeroReference', TextType::class, [
                'label'    => 'Numéro de référence',
                'required' => false,
                'attr'     => ['placeholder' => 'Ex: DOS-2026-001'],
            ])
            ->add('client', EntityType::class, [
                'class'        => Client::class,
                // Only show clients that the current user can see.
                // We use a query builder that leverages the direct avocat relation.
                'query_builder' => function (EntityRepository $er) {
                    $user = $this->security->getUser();
                    if ($this->security->isGranted('ROLE_ADMIN')) {
                        return $er->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                    }
                    
                    $avocatId = null;
                    if (in_array('ROLE_AVOCAT', $user->getRoles())) {
                         $avocatId = $user->getId();
                    } elseif (in_array('ROLE_ASSISTANT', $user->getRoles()) && $user->getManager()) {
                         $avocatId = $user->getManager()->getId();
                    }
                    
                    if ($avocatId) {
                         return $er->createQueryBuilder('c')
                            ->leftJoin('c.dossiers', 'd')
                            ->andWhere('c.avocat = :avocat OR d.avocat = :avocat')
                            ->setParameter('avocat', $avocatId)
                            ->orderBy('c.nom', 'ASC');
                    }
                    return $er->createQueryBuilder('c')->where('1 = 0'); // Fallback safe
                },
                'choice_label' => fn(Client $c) => $c->getFullName() . ($c->getVille() ? ' — ' . $c->getVille() : ''),
                'label'        => 'Client *',
                'placeholder'  => '-- Sélectionner un client --',
                'disabled'     => $isEdit,
            ])
        ;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('avocat', EntityType::class, [
                'class'        => User::class,
                'choice_label' => fn(User $u) => $u->getFullName() ?: $u->getEmail(),
                'label'        => 'Avocat responsable',
                'required'     => false,
                'placeholder'  => '-- Sélectionner un avocat --',
            ]);
        }

        $builder
            ->add('typeCas', ChoiceType::class, [
                'label'   => 'Type de cas',
                'choices' => [
                    'Civil'          => 'Civil',
                    'Pénal'          => 'Pénal',
                    'Commercial'     => 'Commercial',
                    'Familial'       => 'Familial',
                    'Travail'        => 'Travail',
                    'Administratif'  => 'Administratif',
                    'Autre'          => 'Autre',
                ],
                'placeholder' => '-- Type de cas --',
                'required'    => false,
            ])
            ->add('statuts', ChoiceType::class, [
                'label'   => 'Statut',
                'choices' => [
                    'En cours'  => 'En cours',
                    'Clôturé'   => 'Clôturé',
                    'Suspendu'  => 'Suspendu',
                    'Archivé'   => 'Archivé',
                ],
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
            ->add('dateDebut', DateType::class, [
                'label'    => 'Date de début',
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('dateFin', DateType::class, [
                'label'    => 'Date de fin prévue',
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('nomTribunal', TextType::class, [
                'label'    => 'Tribunal compétent',
                'required' => false,
                'attr'     => ['placeholder' => 'Ex: Tribunal de Grande Instance de Paris'],
            ])
            ->add('nomAdversaire', TextType::class, [
                'label'    => 'Partie adverse',
                'required' => false,
                'attr'     => ['placeholder' => 'Nom de la partie adverse'],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description / Notes',
                'required' => false,
                'attr'     => ['rows' => 4, 'placeholder' => 'Résumé du dossier, contexte, notes importantes...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dossier::class,
        ]);
    }
}