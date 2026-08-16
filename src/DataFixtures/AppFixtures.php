<?php

namespace App\DataFixtures;

use App\Entity\ArticleFacture;
use App\Entity\Client;
use App\Entity\Document;
use App\Entity\Dossier;
use App\Entity\EntreeDeTemps;
use App\Entity\Factures;
use App\Entity\Tache;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        try {
            $faker = Factory::create('fr_FR');

            $prenomsAr = ['Mohamed', 'Ali', 'Salah', 'Ahmed', 'Youssef', 'Sami', 'Omar', 'Karim', 'Mehdi', 'Amine', 'Walid', 'Khaled', 'Fatma', 'Meriem', 'Aicha', 'Sarah', 'Leila', 'Amel', 'Nour', 'Sarra'];
            $nomsAr = ['Ben Ali', 'Trabelsi', 'Gharbi', 'Ayari', 'Mathlouthi', 'Jaziri', 'Bouazizi', 'Mabrouk', 'Mansouri', 'Khmiri', 'Driss', 'Cherif', 'Touati', 'Ben Ammar', 'Zitouni'];
            $villesTn = ['Tunis', 'Nabeul', 'Sousse', 'Sfax', 'Bizerte', 'Kairouan', 'Gabès', 'Monastir', 'Hammamet', 'Mahdia', 'Djerba', 'Tataouine', 'Gafsa', 'Béja'];
            $ruesTn = ['Avenue Habib Bourguiba', 'Rue de la Liberté', 'Avenue de Paris', 'Rue de Carthage', 'Avenue Hédi Nouira', 'Rue 9 Avril', 'Avenue Farhat Hached', 'Rue Charles de Gaulle', 'Route de la Plage', 'Avenue de l\'Environnement'];
            $entreprisesTn = ['Société Tunisienne de Commerce', 'Groupe Ben Ammar', 'Tunisie Informatique', 'Meubles Trabelsi', 'Gharbi Import-Export', 'Ayari Construction', 'Clinique Hannibal', 'Industries Zitouni', 'Cabinet d\'Architecture Driss', 'Tech Solutions Sousse'];

            // ======== USERS ========
            $users = [];

            // 1 Admin
            $admin = new User();
            $admin->setEmail('admin@lexpro.fr')
                  ->setRoles(['ROLE_ADMIN'])
                  ->setPassword($this->passwordHasher->hashPassword($admin, 'admin'))
                  ->setPrenom('Mohamed')
                  ->setNom('Ben Salah')
                  ->setTelephone('+216 ' . rand(20000000, 99999999));
            $manager->persist($admin);
            $users[] = $admin;

            // 2 Avocats
            for ($i = 1; $i <= 2; $i++) {
                $avocat = new User();
                $avocat->setEmail("avocat$i@lexpro.fr")
                       ->setRoles(['ROLE_AVOCAT'])
                       ->setPassword($this->passwordHasher->hashPassword($avocat, 'avocat'))
                       ->setPrenom($faker->randomElement($prenomsAr))
                       ->setNom($faker->randomElement($nomsAr))
                       ->setTelephone('+216 ' . rand(20000000, 99999999));
                $manager->persist($avocat);
                $users[] = $avocat;
            }

            // 1 Assistant
            $assistant = new User();
            $assistant->setEmail('assistant@lexpro.fr')
                       ->setRoles(['ROLE_ASSISTANT'])
                       ->setPassword($this->passwordHasher->hashPassword($assistant, 'assistant'))
                       ->setPrenom($faker->randomElement($prenomsAr))
                       ->setNom($faker->randomElement($nomsAr))
                       ->setTelephone('+216 ' . rand(20000000, 99999999));
            $manager->persist($assistant);
            $users[] = $assistant;

            $manager->flush(); // IMPORTANT: Flush users to generate IDs

            // ======== CLIENTS ========
            $clients = [];
            $types = ['particulier', 'entreprise'];
            $statutsClient = ['Prospect', 'Actif', 'Inactif'];

            
            for ($i = 0; $i < 15; $i++) {
                $type = $faker->randomElement($types);
                $client = new Client();
                $client->setType($type)
                       ->setEmail($faker->email)
                       ->setTelephone('+216 ' . rand(20000000, 99999999))
                       ->setAdresse(rand(1, 150) . ' ' . $faker->randomElement($ruesTn))
                       ->setVille($faker->randomElement($villesTn))
                       ->setStatuts($faker->randomElement($statutsClient))
                       ->setAvocat($faker->randomElement([$users[0], $users[1], $users[2]]))
                       ->setRemarques($faker->boolean(30) ? $faker->realText(100) : null);
                
                if ($type === 'entreprise') {
                    $client->setNomEntreprise($faker->randomElement($entreprisesTn))
                           ->setTaxId('MAT-' . rand(100000, 999999) . '-T')
                           ->setNom($faker->randomElement($nomsAr))
                           ->setPrenom($faker->randomElement($prenomsAr));
                } else {
                    $client->setNom($faker->randomElement($nomsAr))
                           ->setPrenom($faker->randomElement($prenomsAr));
                }
                
                $manager->persist($client);
                $clients[] = $client;
            }

            // ======== DOSSIERS ========
            $dossiers = [];
            $statutsDossier = ['En cours', 'Suspendu', 'Clôturé', 'Archivé'];
            $priorites = ['Basse', 'Normale', 'Haute', 'Urgente'];
            $typesCas = ['Droit pénal', 'Droit des affaires', 'Droit familial', 'Droit immobilier', 'Droit du travail'];

            for ($i = 0; $i < 25; $i++) {
                $dossier = new Dossier();
                $dateDebut = $faker->dateTimeBetween('-1 year', 'now');
                $dossier->setClient($faker->randomElement($clients))
                        ->setAvocat($faker->randomElement([$users[0], $users[1], $users[2]])) // Admin or Avocat
                        ->setTitre($faker->sentence(5))
                        ->setDescription($faker->realText(200))
                        ->setTypeCas($faker->randomElement($typesCas))
                        ->setStatuts($faker->randomElement($statutsDossier))
                        ->setPriorite($faker->randomElement($priorites))
                        ->setDateDebut($dateDebut);
                
                if ($faker->boolean(40)) {
                    $dossier->setNomTribunal("Tribunal de Première Instance de " . $faker->randomElement($villesTn))
                            ->setNomAdversaire($faker->randomElement($prenomsAr) . ' ' . $faker->randomElement($nomsAr));
                }
                if (in_array($dossier->getStatuts(), ['Clôturé', 'Archivé'])) {
                    $dateFin = (clone $dateDebut)->modify('+' . rand(1, 6) . ' months');
                    $dossier->setDateFin($dateFin);
                }
                // Auto ref will be generated on logic layer or pre persist
                $dossier->setNumeroReference("REF-" . date('Y') . "-" . str_pad($i + 1, 4, '0', STR_PAD_LEFT));

                $manager->persist($dossier);
                $dossiers[] = $dossier;
            }

            // ======== TACHES ========
            $statutsTache = ['À faire', 'En cours', 'Terminée'];
            for ($i = 0; $i < 50; $i++) {
                $tache = new Tache();
                $dossier = $faker->randomElement($dossiers);
                // Assigner soit à l'avocat du dossier, soit à l'assistant
                $assigne = $faker->boolean(70) ? $dossier->getAvocat() : $users[3];

                $tache->setDossier($dossier)
                      ->setAssigneA($assigne)
                      ->setTitre($faker->sentence(4))
                      ->setDescription($faker->boolean(60) ? $faker->realText(150) : null)
                      ->setPriorite($faker->randomElement($priorites))
                      ->setStatus($faker->randomElement($statutsTache));
                
                if ($tache->getStatus() !== 'Terminée') {
                    $tache->setDateEcheance($faker->dateTimeBetween('-1 week', '+1 month'));
                }

                $manager->persist($tache);
            }

            // ======== ENTREES DE TEMPS ========
            for ($i = 0; $i < 80; $i++) {
                $entree = new EntreeDeTemps();
                $dossier = $faker->randomElement($dossiers);
                
                $entree->setDossier($dossier)
                       ->setUser($dossier->getAvocat() ?? $faker->randomElement([$users[0], $users[1], $users[2]]))
                       ->setDate($faker->dateTimeBetween($dossier->getDateDebut(), 'now'))
                       ->setHeures($faker->randomFloat(1, 0.5, 8.0))
                       ->setDescription("Travail sur le dossier: " . $faker->sentence(6))
                       ->setFacturable($faker->boolean(80));

                $manager->persist($entree);
            }

            // ======== FACTURES & ARTICLES ========
            $statusFacture = ['Brouillon', 'En attente', 'Payée', 'Impayée', 'Annulée'];
            for ($i = 0; $i < 30; $i++) {
                $facture = new Factures();
                $dossier = $faker->randomElement($dossiers);
                $dateEmission = $faker->dateTimeBetween('-6 months', 'now');

                $facture->setClient($dossier->getClient())
                        ->setDossier($dossier)
                        ->setNumeroFacture("F-" . $dateEmission->format('Y') . "-" . str_pad($i + 1, 4, '0', STR_PAD_LEFT))
                        ->setDateEmission($dateEmission)
                        ->setStatus($faker->randomElement($statusFacture))
                        ->setTva(19.0); // TVA Tunisienne
                
                if ($facture->getStatus() === 'En attente' || $facture->getStatus() === 'Impayée') {
                    $facture->setDateEcheance((clone $dateEmission)->modify('+30 days'));
                }

                // Articles
                $nbArticles = rand(1, 5);
                $totalHT = 0;
                for ($j = 0; $j < $nbArticles; $j++) {
                    $article = new ArticleFacture();
                    $article->setFacture($facture)
                            ->setDescription($faker->sentence(5))
                            ->setQuantite($faker->randomFloat(1, 1, 15))
                            ->setPrixUnitaire($faker->randomFloat(2, 50, 350));
                    
                    $manager->persist($article);
                    $totalHT += ($article->getQuantite() * $article->getPrixUnitaire());
                }

                $tva = $totalHT * 0.19; // TVA Tunisienne
                $facture->setMontantHt($totalHT);
                $facture->setMontantTtc($totalHT + $tva);

                $manager->persist($facture);
            }

            // ======== DOCUMENTS (MOCK) ========
            $typesDocs = ['Contrat', 'Jugement', 'Plainte', 'Procuration', 'Justificatif', 'Correspondance', 'Autre'];
            $confidentialite = ['Public', 'Interne', 'Confidentiel', 'Secret'];
            
            for ($i = 0; $i < 40; $i++) {
                $doc = new Document();
                $dossier = $faker->randomElement($dossiers);
                
                $doc->setDossier($dossier)
                    ->setTitre($faker->words(3, true))
                    ->setNomOriginal($faker->word . '.pdf')
                    ->setCheminFichier('mock_path_' . $i . '.pdf')
                    ->setType($faker->randomElement($typesDocs))
                    ->setConfidentialite($faker->randomElement($confidentialite))
                    ->setTelechargepar($faker->randomElement($users));
                    
                $manager->persist($doc);
            }

            $manager->flush();
        } catch (\Throwable $e) {
            file_put_contents('fixture_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }
}
