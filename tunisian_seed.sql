-- ============================================================
-- LexPro - Données tunisiennes réalistes
-- Base de données: lexpro_dev
-- Mot de passe pour tous les utilisateurs: password123
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Vider toutes les tables (ordre inversé pour FK)
TRUNCATE TABLE notification;
TRUNCATE TABLE article_facture;
TRUNCATE TABLE factures;
TRUNCATE TABLE document;
TRUNCATE TABLE entree_de_temps;
TRUNCATE TABLE tache;
TRUNCATE TABLE rendez_vous;
TRUNCATE TABLE dossier;
TRUNCATE TABLE client;
TRUNCATE TABLE user;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- USERS (6 utilisateurs)
-- Mot de passe: password123 (hash bcrypt)
-- ============================================================
INSERT INTO user (id, email, roles, password, prenom, nom, telephone, created_at, manager_id, image) VALUES
-- Admin
(1, 'admin@lexpro.tn', '["ROLE_ADMIN"]', '$2y$12$fLYQEEO3CRiTmgCRgd.msOTYce7oFaWXW9UGUTSPoPaRSngw7R30q', 'Mohamed', 'Ben Salah', '+216 71 234 567', '2025-01-15 08:00:00', NULL, NULL),
-- Avocats
(2, 'avocat1@lexpro.tn', '["ROLE_AVOCAT"]', '$2y$12$fLYQEEO3CRiTmgCRgd.msOTYce7oFaWXW9UGUTSPoPaRSngw7R30q', 'Sami', 'Trabelsi', '+216 98 456 123', '2025-02-01 09:00:00', NULL, NULL),
(3, 'avocat2@lexpro.tn', '["ROLE_AVOCAT"]', '$2y$12$fLYQEEO3CRiTmgCRgd.msOTYce7oFaWXW9UGUTSPoPaRSngw7R30q', 'Leila', 'Gharbi', '+216 97 789 456', '2025-02-10 09:00:00', NULL, NULL),
(4, 'avocat3@lexpro.tn', '["ROLE_AVOCAT"]', '$2y$12$fLYQEEO3CRiTmgCRgd.msOTYce7oFaWXW9UGUTSPoPaRSngw7R30q', 'Karim', 'Ayari', '+216 55 321 987', '2025-03-01 09:00:00', NULL, NULL),
-- Assistants (liés à des avocats)
(5, 'assistant1@lexpro.tn', '["ROLE_ASSISTANT"]', '$2y$12$fLYQEEO3CRiTmgCRgd.msOTYce7oFaWXW9UGUTSPoPaRSngw7R30q', 'Maram', 'Jaziri', '+216 52 654 321', '2025-03-15 09:00:00', 2, NULL),
(6, 'assistant2@lexpro.tn', '["ROLE_ASSISTANT"]', '$2y$12$fLYQEEO3CRiTmgCRgd.msOTYce7oFaWXW9UGUTSPoPaRSngw7R30q', 'Nour', 'Mabrouk', '+216 54 987 654', '2025-04-01 09:00:00', 3, NULL);

-- ============================================================
-- CLIENTS (20 clients - particuliers et entreprises)
-- ============================================================
INSERT INTO client (id, type, prenom, nom, nom_entreprise, tax_id, telephone, email, adresse, ville, remarques, statuts, created_at, image, avocat_id) VALUES
-- Particuliers
(1,  'particulier', 'Ahmed',   'Mathlouthi', NULL, NULL, '+216 98 111 222', 'ahmed.mathlouthi@gmail.com', '15 Avenue Habib Bourguiba', 'Tunis', 'Client fidèle depuis 2023', 'Actif', '2025-03-01 10:00:00', NULL, 2),
(2,  'particulier', 'Fatma',   'Bouazizi',   NULL, NULL, '+216 97 333 444', 'fatma.bouazizi@yahoo.fr', '22 Rue de la Liberté', 'Sousse', NULL, 'Actif', '2025-03-05 11:00:00', NULL, 2),
(3,  'particulier', 'Youssef', 'Mansouri',   NULL, NULL, '+216 55 555 666', 'youssef.mansouri@outlook.com', '8 Avenue de Carthage', 'Sfax', 'Dossier complexe en cours', 'Actif', '2025-03-10 14:00:00', NULL, 3),
(4,  'particulier', 'Aicha',   'Khmiri',     NULL, NULL, '+216 52 777 888', 'aicha.khmiri@gmail.com', '45 Rue 9 Avril', 'Bizerte', NULL, 'Actif', '2025-04-01 09:30:00', NULL, 3),
(5,  'particulier', 'Salah',   'Cherif',     NULL, NULL, '+216 98 999 000', 'salah.cherif@hotmail.com', '3 Avenue Farhat Hached', 'Kairouan', NULL, 'Prospect', '2025-04-10 10:00:00', NULL, 4),
(6,  'particulier', 'Meriem',  'Touati',     NULL, NULL, '+216 97 112 334', 'meriem.touati@gmail.com', '67 Route de la Plage', 'Hammamet', 'Consultation initiale effectuée', 'Actif', '2025-04-15 15:00:00', NULL, 2),
(7,  'particulier', 'Omar',    'Driss',      NULL, NULL, '+216 55 445 667', 'omar.driss@yahoo.fr', '12 Avenue de Paris', 'Monastir', NULL, 'Actif', '2025-05-01 08:30:00', NULL, 4),
(8,  'particulier', 'Sarah',   'Ben Ammar',  NULL, NULL, '+216 52 778 990', 'sarah.benammar@gmail.com', '30 Rue Charles de Gaulle', 'Nabeul', NULL, 'Inactif', '2025-05-10 11:00:00', NULL, 3),
(9,  'particulier', 'Mehdi',   'Zitouni',    NULL, NULL, '+216 98 223 445', 'mehdi.zitouni@outlook.com', '5 Avenue de l\'Environnement', 'Gabès', 'Litige immobilier', 'Actif', '2025-05-20 14:30:00', NULL, 2),
(10, 'particulier', 'Sarra',   'Ben Ali',    NULL, NULL, '+216 97 556 778', 'sarra.benali@gmail.com', '18 Rue de la République', 'Mahdia', NULL, 'Actif', '2025-06-01 09:00:00', NULL, 4),
(11, 'particulier', 'Walid',   'Gharbi',     NULL, NULL, '+216 55 889 001', 'walid.gharbi@hotmail.com', '41 Avenue de la Liberté', 'Djerba', NULL, 'Prospect', '2025-06-05 10:30:00', NULL, 3),
(12, 'particulier', 'Amel',    'Ayari',      NULL, NULL, '+216 52 112 233', 'amel.ayari@yahoo.fr', '7 Rue Ibn Khaldoun', 'Gafsa', 'Ancienne cliente - dossier clôturé', 'Inactif', '2025-06-10 16:00:00', NULL, 2),
-- Entreprises
(13, 'entreprise', 'Khaled', 'Trabelsi', 'Groupe Trabelsi Import-Export', 'MAT-456789-T', '+216 71 456 789', 'contact@trabelsi-group.tn', '100 Avenue Hédi Nouira', 'Tunis', 'Client stratégique - gros volume', 'Actif', '2025-03-20 09:00:00', NULL, 2),
(14, 'entreprise', 'Amine',  'Jaziri',   'Société Jaziri Construction SARL', 'MAT-567890-T', '+216 73 567 890', 'admin@jaziri-construction.tn', '55 Zone Industrielle', 'Sousse', NULL, 'Actif', '2025-04-05 10:00:00', NULL, 3),
(15, 'entreprise', 'Ali',    'Mansouri', 'Tech Solutions Tunisie', 'MAT-678901-T', '+216 74 678 901', 'info@techsolutions.tn', '22 Technopole El Ghazala', 'Tunis', 'Contrat annuel de conseil juridique', 'Actif', '2025-04-20 11:30:00', NULL, 4),
(16, 'entreprise', 'Mohamed','Bouazizi', 'Clinique El Amal', 'MAT-789012-T', '+216 72 789 012', 'direction@clinique-elamal.tn', '10 Avenue de la Santé', 'Monastir', NULL, 'Actif', '2025-05-01 08:00:00', NULL, 2),
(17, 'entreprise', 'Sami',   'Mathlouthi','Meubles Mathlouthi et Fils', 'MAT-890123-T', '+216 74 890 123', 'ventes@meubles-mathlouthi.tn', '33 Rue de l\'Artisanat', 'Sfax', NULL, 'Prospect', '2025-05-15 14:00:00', NULL, 3),
(18, 'entreprise', 'Nour',   'Khmiri',   'Agence Immobilière Khmiri', 'MAT-901234-T', '+216 72 901 234', 'agence@khmiri-immo.tn', '8 Boulevard de l\'Indépendance', 'Bizerte', 'Plusieurs dossiers en parallèle', 'Actif', '2025-06-01 09:30:00', NULL, 4),
(19, 'entreprise', 'Leila',  'Cherif',   'Cabinet Comptable Cherif', 'MAT-012345-T', '+216 71 012 345', 'cabinet@cherif-compta.tn', '15 Rue de Rome', 'Tunis', NULL, 'Actif', '2025-06-10 10:00:00', NULL, 2),
(20, 'entreprise', 'Karim',  'Touati',   'Hôtel Hannibal Palace', 'MAT-123456-T', '+216 73 123 456', 'admin@hannibal-palace.tn', '1 Avenue de la Plage', 'Hammamet', 'Litige avec sous-traitant', 'Actif', '2025-06-15 11:00:00', NULL, 3);

-- ============================================================
-- DOSSIERS (30 dossiers)
-- ============================================================
INSERT INTO dossier (id, client_id, avocat_id, titre, numero_reference, description, type_cas, statuts, priorite, date_debut, date_fin, nom_tribunal, nom_adversaire) VALUES
-- Dossiers de Me Sami Trabelsi (avocat_id=2)
(1,  1,  2, 'Litige locatif - Appartement Lac 1', 'REF-2025-0001', 'Litige entre propriétaire et locataire concernant le paiement des loyers en retard et les réparations non effectuées.', 'Civil', 'En cours', 'Haute', '2025-03-15', NULL, 'Tribunal de Première Instance de Tunis', 'Hichem Baccouche'),
(2,  2,  2, 'Accident de la route - Indemnisation', 'REF-2025-0002', 'Demande d\'indemnisation suite à un accident de la route survenu sur l\'autoroute Tunis-Sousse.', 'Civil', 'En cours', 'Urgente', '2025-04-01', NULL, 'Tribunal de Première Instance de Sousse', 'Assurance STAR'),
(3,  6,  2, 'Divorce par consentement mutuel', 'REF-2025-0003', 'Procédure de divorce amiable avec partage des biens et garde des enfants.', 'Familial', 'En cours', 'Normale', '2025-05-01', NULL, NULL, NULL),
(4,  9,  2, 'Vente immobilière contestée', 'REF-2025-0004', 'Contestation d\'une vente immobilière pour vice caché sur un terrain situé à Gabès.', 'Civil', 'Suspendu', 'Haute', '2025-04-20', NULL, 'Tribunal Immobilier de Gabès', 'Société Immobilière du Sud'),
(5,  12, 2, 'Recouvrement de créances', 'REF-2025-0005', 'Procédure de recouvrement de créances impayées d\'un montant total de 45 000 DT.', 'Commercial', 'Clôturé', 'Normale', '2025-02-10', '2025-05-15', 'Tribunal de Commerce de Tunis', 'SARL Ben Ahmed'),
(6,  13, 2, 'Contrat de distribution - Rédaction', 'REF-2025-0006', 'Rédaction et négociation d\'un contrat de distribution exclusive pour le marché tunisien.', 'Commercial', 'En cours', 'Normale', '2025-06-01', NULL, NULL, NULL),
(7,  16, 2, 'Agrément sanitaire - Clinique', 'REF-2025-0007', 'Accompagnement juridique pour l\'obtention de l\'agrément sanitaire de la nouvelle aile.', 'Administratif', 'En cours', 'Haute', '2025-05-15', NULL, 'Tribunal Administratif de Monastir', NULL),
(8,  19, 2, 'Litige fiscal - Redressement', 'REF-2025-0008', 'Contestation d\'un redressement fiscal de 120 000 DT auprès de l\'administration fiscale.', 'Administratif', 'En cours', 'Urgente', '2025-06-10', NULL, 'Tribunal Administratif de Tunis', 'Direction Générale des Impôts'),
(9,  1,  2, 'Succession Ben Mathlouthi', 'REF-2025-0009', 'Gestion de la succession et partage des biens hérités entre les trois héritiers.', 'Familial', 'En cours', 'Normale', '2025-06-15', NULL, NULL, NULL),
(10, 13, 2, 'Contrat de travail cadres - Groupe Trabelsi', 'REF-2025-0010', 'Rédaction de contrats de travail pour 5 cadres supérieurs du groupe.', 'Travail', 'Clôturé', 'Basse', '2025-03-01', '2025-04-30', NULL, NULL),

-- Dossiers de Me Leila Gharbi (avocat_id=3)
(11, 3,  3, 'Licenciement abusif - Usine Sfax', 'REF-2025-0011', 'Défense d\'un employé licencié abusivement après 12 ans de service dans une usine textile.', 'Travail', 'En cours', 'Haute', '2025-04-05', NULL, 'Tribunal de Première Instance de Sfax', 'Société Textile du Sahel'),
(12, 4,  3, 'Héritage contesté - Terrain Bizerte', 'REF-2025-0012', 'Litige successoral concernant un terrain agricole de 5 hectares à Bizerte.', 'Familial', 'En cours', 'Urgente', '2025-04-15', NULL, 'Tribunal Immobilier de Bizerte', 'Famille Bouzid'),
(13, 8,  3, 'Défense pénale - Diffamation', 'REF-2025-0013', 'Défense contre une plainte de diffamation sur les réseaux sociaux.', 'Pénal', 'Archivé', 'Normale', '2025-02-01', '2025-05-01', 'Tribunal de Première Instance de Nabeul', 'Slim Bouzid'),
(14, 11, 3, 'Consultation - Création société', 'REF-2025-0014', 'Conseil juridique pour la création d\'une SARL de commerce en ligne à Djerba.', 'Commercial', 'Clôturé', 'Basse', '2025-05-20', '2025-06-10', NULL, NULL),
(15, 14, 3, 'Litige chantier - Malfaçons', 'REF-2025-0015', 'Litige entre Jaziri Construction et un sous-traitant pour malfaçons sur un chantier.', 'Commercial', 'En cours', 'Haute', '2025-05-01', NULL, 'Tribunal de Commerce de Sousse', 'Entreprise Ben Miled BTP'),
(16, 17, 3, 'Marque commerciale - Enregistrement', 'REF-2025-0016', 'Dépôt et enregistrement de la marque commerciale Meubles Mathlouthi à l\'INNORPI.', 'Commercial', 'En cours', 'Normale', '2025-06-01', NULL, NULL, NULL),
(17, 20, 3, 'Litige sous-traitance hôtelière', 'REF-2025-0017', 'Rupture de contrat de sous-traitance pour les services de restauration de l\'hôtel.', 'Commercial', 'En cours', 'Haute', '2025-06-15', NULL, 'Tribunal de Commerce de Nabeul', 'Catering Plus SARL'),
(18, 3,  3, 'Pension alimentaire - Révision', 'REF-2025-0018', 'Demande de révision à la hausse de la pension alimentaire pour deux enfants mineurs.', 'Familial', 'En cours', 'Normale', '2025-06-20', NULL, 'Tribunal de Première Instance de Sfax', 'Nadia Mansouri'),
(19, 4,  3, 'Permis de construire refusé', 'REF-2025-0019', 'Recours administratif contre le refus de permis de construire par la municipalité de Bizerte.', 'Administratif', 'Suspendu', 'Haute', '2025-05-10', NULL, 'Tribunal Administratif de Bizerte', 'Municipalité de Bizerte'),
(20, 14, 3, 'Contrat avec promoteur - Jaziri', 'REF-2025-0020', 'Rédaction du contrat de promotion immobilière pour le nouveau projet résidentiel.', 'Commercial', 'En cours', 'Normale', '2025-06-18', NULL, NULL, NULL),

-- Dossiers de Me Karim Ayari (avocat_id=4)
(21, 5,  4, 'Agression physique - Défense', 'REF-2025-0021', 'Défense d\'un client accusé d\'agression physique lors d\'un différend commercial.', 'Pénal', 'En cours', 'Urgente', '2025-05-01', NULL, 'Tribunal de Première Instance de Kairouan', 'Rached Hamdi'),
(22, 7,  4, 'Expulsion locataire - Monastir', 'REF-2025-0022', 'Procédure d\'expulsion d\'un locataire pour non-paiement de loyer depuis 8 mois.', 'Civil', 'En cours', 'Haute', '2025-05-15', NULL, 'Tribunal Cantonal de Monastir', 'Nabil Oueslati'),
(23, 10, 4, 'Accident de travail - Indemnisation', 'REF-2025-0023', 'Demande d\'indemnisation pour accident de travail dans une usine de conserves.', 'Travail', 'En cours', 'Haute', '2025-06-01', NULL, 'Tribunal de Première Instance de Mahdia', 'Conserverie du Cap Bon'),
(24, 15, 4, 'Contrat SaaS - Tech Solutions', 'REF-2025-0024', 'Rédaction des CGU et contrats SaaS pour la plateforme de gestion hospitalière.', 'Commercial', 'En cours', 'Normale', '2025-05-20', NULL, NULL, NULL),
(25, 18, 4, 'Litige bail commercial - Khmiri Immo', 'REF-2025-0025', 'Litige concernant la résiliation anticipée d\'un bail commercial à Bizerte.', 'Commercial', 'En cours', 'Haute', '2025-06-05', NULL, 'Tribunal de Commerce de Bizerte', 'SARL Electroménager du Nord'),
(26, 5,  4, 'Vol avec effraction - Défense', 'REF-2025-0026', 'Défense pénale dans une affaire de vol avec effraction présumé.', 'Pénal', 'En cours', 'Urgente', '2025-06-12', NULL, 'Tribunal de Première Instance de Kairouan', 'Ministère Public'),
(27, 7,  4, 'Trouble de voisinage', 'REF-2025-0027', 'Litige civil pour troubles de voisinage - nuisances sonores d\'un café.', 'Civil', 'Suspendu', 'Basse', '2025-05-25', NULL, NULL, 'Café El Medina'),
(28, 15, 4, 'Protection données personnelles', 'REF-2025-0028', 'Mise en conformité avec la loi organique n°2004-63 relative à la protection des données personnelles.', 'Administratif', 'En cours', 'Normale', '2025-06-15', NULL, NULL, NULL),
(29, 18, 4, 'Contrat location saisonnière', 'REF-2025-0029', 'Rédaction de modèles de contrats de location saisonnière pour le portefeuille Khmiri Immo.', 'Civil', 'Clôturé', 'Basse', '2025-04-01', '2025-05-20', NULL, NULL),
(30, 10, 4, 'Harcèlement moral au travail', 'REF-2025-0030', 'Plainte pour harcèlement moral au travail avec demande de dommages et intérêts.', 'Travail', 'En cours', 'Urgente', '2025-06-20', NULL, 'Tribunal de Première Instance de Mahdia', 'Directeur Usine SORECO');

-- ============================================================
-- TACHES (60 tâches)
-- ============================================================
INSERT INTO tache (id, dossier_id, assigne_a_id, titre, description, date_echeance, status, priorite) VALUES
-- Tâches pour dossier 1 (Litige locatif)
(1,  1, 2, 'Préparer la mise en demeure', 'Rédiger et envoyer une mise en demeure au locataire pour paiement des arriérés de loyer.', '2025-07-01', 'Terminée', 'Haute'),
(2,  1, 5, 'Rassembler les preuves de loyers impayés', 'Collecter les relevés bancaires et quittances de loyer manquantes.', '2025-07-05', 'En cours', 'Normale'),
(3,  1, 2, 'Rédiger l\'assignation au tribunal', 'Préparer l\'assignation devant le Tribunal de Première Instance de Tunis.', '2025-07-15', 'À faire', 'Haute'),

-- Tâches pour dossier 2 (Accident de route)
(4,  2, 2, 'Obtenir le rapport de police', 'Demander une copie du procès-verbal de l\'accident auprès du poste de police.', '2025-07-03', 'Terminée', 'Urgente'),
(5,  2, 5, 'Contacter l\'expert médical', 'Prendre rendez-vous avec le Dr. Ben Amor pour l\'expertise médicale.', '2025-07-08', 'Terminée', 'Haute'),
(6,  2, 2, 'Évaluer le préjudice total', 'Calculer le montant total du préjudice (corporel + matériel + moral).', '2025-07-20', 'En cours', 'Haute'),

-- Tâches pour dossier 3 (Divorce)
(7,  3, 2, 'Rédiger la convention de divorce', 'Préparer le projet de convention de divorce par consentement mutuel.', '2025-07-10', 'En cours', 'Normale'),
(8,  3, 5, 'Inventaire des biens communs', 'Dresser la liste complète des biens immobiliers et mobiliers du couple.', '2025-07-15', 'À faire', 'Normale'),

-- Tâches pour dossier 6 (Contrat distribution)
(9,  6, 2, 'Analyser le marché tunisien', 'Étude juridique des réglementations applicables au contrat de distribution.', '2025-07-05', 'Terminée', 'Normale'),
(10, 6, 5, 'Rédiger les clauses de distribution', 'Préparer le projet de contrat avec les clauses d\'exclusivité territoriale.', '2025-07-20', 'En cours', 'Haute'),

-- Tâches pour dossier 8 (Litige fiscal)
(11, 8, 2, 'Analyser l\'avis de redressement', 'Étude détaillée de l\'avis de redressement fiscal et identification des erreurs.', '2025-06-25', 'Terminée', 'Urgente'),
(12, 8, 5, 'Préparer le dossier de réclamation', 'Rassembler les pièces justificatives pour la réclamation fiscale.', '2025-07-10', 'En cours', 'Urgente'),
(13, 8, 2, 'Déposer le recours administratif', 'Déposer le recours gracieux auprès de la DGI.', '2025-07-25', 'À faire', 'Urgente'),

-- Tâches pour dossier 11 (Licenciement abusif)
(14, 11, 3, 'Étudier le dossier de l\'employé', 'Analyser le contrat de travail et les bulletins de paie.', '2025-07-01', 'Terminée', 'Haute'),
(15, 11, 6, 'Collecter les témoignages', 'Recueillir les témoignages des collègues de travail.', '2025-07-10', 'En cours', 'Haute'),
(16, 11, 3, 'Préparer les conclusions', 'Rédiger les conclusions pour l\'audience du tribunal.', '2025-07-20', 'À faire', 'Haute'),

-- Tâches pour dossier 12 (Héritage contesté)
(17, 12, 3, 'Vérifier les titres de propriété', 'Obtenir les titres de propriété du terrain au registre foncier.', '2025-07-05', 'Terminée', 'Urgente'),
(18, 12, 6, 'Rechercher les documents de succession', 'Retrouver le testament et les actes notariés.', '2025-07-12', 'En cours', 'Haute'),
(19, 12, 3, 'Engager la procédure judiciaire', 'Préparer l\'assignation devant le Tribunal Immobilier de Bizerte.', '2025-07-25', 'À faire', 'Urgente'),

-- Tâches pour dossier 15 (Litige chantier)
(20, 15, 3, 'Expertise technique du chantier', 'Organiser une expertise judiciaire sur le chantier.', '2025-07-08', 'En cours', 'Haute'),
(21, 15, 6, 'Photographier les malfaçons', 'Documenter photographiquement toutes les malfaçons constatées.', '2025-07-03', 'Terminée', 'Normale'),
(22, 15, 3, 'Rédiger la demande de réparation', 'Chiffrer et demander la réparation des malfaçons.', '2025-07-20', 'À faire', 'Haute'),

-- Tâches pour dossier 17 (Litige sous-traitance)
(23, 17, 3, 'Analyser le contrat de sous-traitance', 'Étudier les clauses de résiliation du contrat de sous-traitance.', '2025-07-01', 'Terminée', 'Haute'),
(24, 17, 6, 'Calculer le préjudice financier', 'Évaluer le manque à gagner lié à la rupture de contrat.', '2025-07-15', 'En cours', 'Haute'),

-- Tâches pour dossier 21 (Agression physique)
(25, 21, 4, 'Étudier le dossier pénal', 'Analyser les PV d\'audition et les certificats médicaux.', '2025-06-28', 'Terminée', 'Urgente'),
(26, 21, 4, 'Préparer la défense', 'Construire la stratégie de défense et identifier les témoins.', '2025-07-10', 'En cours', 'Urgente'),
(27, 21, 4, 'Plaider devant le tribunal', 'Audience devant le Tribunal de Première Instance de Kairouan.', '2025-07-30', 'À faire', 'Urgente'),

-- Tâches pour dossier 22 (Expulsion locataire)
(28, 22, 4, 'Envoyer le commandement de payer', 'Signifier le commandement de payer par huissier.', '2025-06-25', 'Terminée', 'Haute'),
(29, 22, 4, 'Déposer la demande d\'expulsion', 'Saisir le tribunal cantonal pour la procédure d\'expulsion.', '2025-07-10', 'En cours', 'Haute'),

-- Tâches pour dossier 23 (Accident de travail)
(30, 23, 4, 'Obtenir le certificat médical initial', 'Récupérer le certificat médical initial de l\'accident.', '2025-06-30', 'Terminée', 'Haute'),
(31, 23, 4, 'Constituer le dossier CNAM', 'Préparer le dossier de prise en charge par la CNAM.', '2025-07-15', 'En cours', 'Haute'),
(32, 23, 4, 'Évaluer l\'incapacité permanente', 'Faire évaluer le taux d\'IPP par un médecin expert.', '2025-07-25', 'À faire', 'Haute'),

-- Tâches pour dossier 24 (Contrat SaaS)
(33, 24, 4, 'Étudier la réglementation RGPD tunisienne', 'Analyser la loi sur la protection des données personnelles.', '2025-07-05', 'Terminée', 'Normale'),
(34, 24, 4, 'Rédiger les CGU', 'Rédiger les conditions générales d\'utilisation de la plateforme.', '2025-07-15', 'En cours', 'Normale'),
(35, 24, 4, 'Rédiger le contrat SaaS', 'Préparer le contrat d\'abonnement SaaS avec les SLA.', '2025-07-25', 'À faire', 'Normale'),

-- Tâches pour dossier 25 (Bail commercial)
(36, 25, 4, 'Analyser le bail commercial', 'Étudier les clauses du bail et les conditions de résiliation.', '2025-07-01', 'Terminée', 'Haute'),
(37, 25, 4, 'Négocier avec le bailleur', 'Tenter une résolution amiable du litige.', '2025-07-15', 'En cours', 'Haute'),

-- Tâches pour dossier 26 (Vol avec effraction)
(38, 26, 4, 'Consulter le dossier d\'instruction', 'Obtenir et étudier le dossier d\'instruction judiciaire.', '2025-07-01', 'Terminée', 'Urgente'),
(39, 26, 4, 'Identifier les éléments de défense', 'Rechercher les alibis et preuves à décharge.', '2025-07-10', 'En cours', 'Urgente'),

-- Tâches pour dossier 30 (Harcèlement moral)
(40, 30, 4, 'Recueillir les témoignages', 'Collecter les témoignages de collègues sur le harcèlement.', '2025-07-05', 'En cours', 'Urgente'),
(41, 30, 4, 'Constituer le dossier médical', 'Rassembler les certificats médicaux et arrêts de travail.', '2025-07-10', 'À faire', 'Urgente'),

-- Tâches supplémentaires
(42, 4,  2, 'Recherche jurisprudence vices cachés', 'Rechercher la jurisprudence tunisienne en matière de vices cachés immobiliers.', '2025-07-10', 'En cours', 'Haute'),
(43, 7,  5, 'Préparer le dossier administratif', 'Rassembler les documents nécessaires pour l\'agrément sanitaire.', '2025-07-15', 'En cours', 'Haute'),
(44, 9,  2, 'Inventaire des biens successoraux', 'Dresser l\'inventaire complet des biens de la succession.', '2025-07-20', 'À faire', 'Normale'),
(45, 16, 6, 'Préparer le dossier INNORPI', 'Remplir les formulaires et préparer les échantillons pour le dépôt de marque.', '2025-07-10', 'En cours', 'Normale'),
(46, 18, 3, 'Calculer la pension révisée', 'Calculer le nouveau montant de la pension alimentaire.', '2025-07-15', 'En cours', 'Normale'),
(47, 19, 6, 'Préparer le recours administratif', 'Rédiger le mémoire de recours contre le refus de permis.', '2025-07-20', 'À faire', 'Haute'),
(48, 20, 3, 'Rédiger le contrat de promotion', 'Préparer le projet de contrat de promotion immobilière.', '2025-07-15', 'En cours', 'Normale'),
(49, 28, 4, 'Audit de conformité données', 'Réaliser l\'audit de conformité RGPD de Tech Solutions.', '2025-07-20', 'À faire', 'Normale'),
(50, 2,  5, 'Scanner les documents médicaux', 'Numériser tous les documents médicaux du client.', '2025-07-05', 'Terminée', 'Normale'),

-- Tâches supplémentaires variées
(51, 1,  5, 'Appeler le syndic de l\'immeuble', 'Contacter le syndic pour obtenir les règlements de copropriété.', '2025-07-08', 'Terminée', 'Basse'),
(52, 6,  2, 'Vérifier les clauses d\'exclusivité', 'S\'assurer que les clauses respectent le droit de la concurrence.', '2025-07-18', 'À faire', 'Normale'),
(53, 11, 6, 'Contacter l\'inspection du travail', 'Demander les rapports d\'inspection du travail de l\'usine.', '2025-07-12', 'En cours', 'Normale'),
(54, 15, 6, 'Organiser la visite du chantier', 'Coordonner la visite avec l\'expert judiciaire.', '2025-07-06', 'Terminée', 'Haute'),
(55, 22, 4, 'Préparer le constat d\'huissier', 'Organiser un constat d\'huissier de l\'état des lieux.', '2025-07-18', 'À faire', 'Normale'),
(56, 25, 4, 'Calculer les indemnités de rupture', 'Évaluer les indemnités dues en cas de résiliation anticipée.', '2025-07-20', 'À faire', 'Haute'),
(57, 13, 3, 'Archiver le dossier clôturé', 'Numériser et archiver tous les documents du dossier.', '2025-05-05', 'Terminée', 'Basse'),
(58, 14, 3, 'Envoyer les statuts au greffe', 'Déposer les statuts de la SARL au greffe du tribunal.', '2025-06-08', 'Terminée', 'Normale'),
(59, 10, 2, 'Archiver les contrats signés', 'Classer et archiver les 5 contrats de travail signés.', '2025-05-01', 'Terminée', 'Basse'),
(60, 29, 4, 'Finaliser les modèles de contrats', 'Relire et finaliser les modèles de location saisonnière.', '2025-05-18', 'Terminée', 'Normale');

-- ============================================================
-- RENDEZ-VOUS (15 rendez-vous)
-- ============================================================
INSERT INTO rendez_vous (id, date, client_id, status) VALUES
(1,  '2026-07-01 09:00:00', 1,  'Confirmé'),
(2,  '2026-07-02 14:00:00', 3,  'Confirmé'),
(3,  '2026-07-03 10:30:00', 13, 'Confirmé'),
(4,  '2026-07-05 11:00:00', 5,  'En attente'),
(5,  '2026-07-07 09:30:00', 14, 'Confirmé'),
(6,  '2026-07-08 15:00:00', 7,  'En attente'),
(7,  '2026-07-10 10:00:00', 16, 'Confirmé'),
(8,  '2026-07-11 14:30:00', 9,  'Confirmé'),
(9,  '2026-07-14 09:00:00', 18, 'En attente'),
(10, '2026-07-15 11:00:00', 20, 'Confirmé'),
(11, '2026-07-16 10:00:00', 2,  'Confirmé'),
(12, '2026-07-17 14:00:00', 10, 'En attente'),
(13, '2026-07-21 09:30:00', 15, 'Confirmé'),
(14, '2026-07-22 16:00:00', 4,  'Confirmé'),
(15, '2026-07-25 11:00:00', 6,  'En attente');

-- ============================================================
-- ENTREES DE TEMPS (100 entrées)
-- ============================================================
INSERT INTO entree_de_temps (id, user_id, dossier_id, heures, date, description, facturable) VALUES
-- Me Sami Trabelsi - Dossier 1
(1,  2, 1, 2.00, '2025-03-16', 'Étude du dossier et analyse des pièces', 1),
(2,  2, 1, 1.50, '2025-03-20', 'Rédaction de la mise en demeure', 1),
(3,  2, 1, 3.00, '2025-04-02', 'Réunion avec le client et visite des lieux', 1),
(4,  5, 1, 1.00, '2025-04-05', 'Classement et numérisation des pièces', 0),
-- Me Sami - Dossier 2
(5,  2, 2, 2.50, '2025-04-02', 'Première consultation avec le client', 1),
(6,  2, 2, 1.50, '2025-04-10', 'Analyse du rapport de police', 1),
(7,  2, 2, 3.00, '2025-04-20', 'Rédaction de la demande d\'indemnisation', 1),
(8,  5, 2, 0.50, '2025-04-15', 'Appels téléphoniques à l\'assurance', 0),
-- Me Sami - Dossier 3
(9,  2, 3, 2.00, '2025-05-05', 'Entretien avec les deux parties', 1),
(10, 2, 3, 3.00, '2025-05-15', 'Rédaction de la convention de divorce', 1),
-- Me Sami - Dossier 6
(11, 2, 6, 2.00, '2025-06-02', 'Étude du marché et réglementation', 1),
(12, 2, 6, 4.00, '2025-06-10', 'Rédaction du projet de contrat', 1),
(13, 5, 6, 1.50, '2025-06-08', 'Recherches juridiques', 0),
-- Me Sami - Dossier 7
(14, 2, 7, 2.50, '2025-05-16', 'Consultation réglementation sanitaire', 1),
(15, 2, 7, 1.50, '2025-05-25', 'Préparation du dossier administratif', 1),
-- Me Sami - Dossier 8
(16, 2, 8, 4.00, '2025-06-11', 'Analyse détaillée du redressement fiscal', 1),
(17, 2, 8, 3.00, '2025-06-15', 'Préparation de la réclamation fiscale', 1),
(18, 5, 8, 2.00, '2025-06-13', 'Tri et classement des justificatifs', 0),
-- Me Sami - Dossier 9
(19, 2, 9, 1.50, '2025-06-16', 'Premier entretien succession', 1),
(20, 2, 9, 2.00, '2025-06-20', 'Inventaire préliminaire des biens', 1),
-- Me Sami - Dossier 4
(21, 2, 4, 2.00, '2025-04-22', 'Visite du terrain et constat', 1),
(22, 2, 4, 1.50, '2025-05-05', 'Recherche au registre foncier', 1),
-- Me Sami - Dossier 5 (clôturé)
(23, 2, 5, 3.00, '2025-02-15', 'Analyse des créances impayées', 1),
(24, 2, 5, 2.00, '2025-03-10', 'Rédaction de l\'assignation', 1),
(25, 2, 5, 4.00, '2025-04-15', 'Audience au tribunal de commerce', 1),
-- Me Sami - Dossier 10 (clôturé)
(26, 2, 10, 5.00, '2025-03-05', 'Rédaction des 5 contrats de travail', 1),
(27, 2, 10, 2.00, '2025-04-10', 'Révision et finalisation', 1),

-- Me Leila Gharbi - Dossier 11
(28, 3, 11, 2.00, '2025-04-06', 'Étude du dossier de licenciement', 1),
(29, 3, 11, 1.50, '2025-04-15', 'Entretien avec l\'employé', 1),
(30, 3, 11, 3.00, '2025-05-01', 'Recherche jurisprudence droit du travail', 1),
(31, 6, 11, 1.00, '2025-04-20', 'Organisation des pièces du dossier', 0),
-- Me Leila - Dossier 12
(32, 3, 12, 2.50, '2025-04-16', 'Consultation registre foncier Bizerte', 1),
(33, 3, 12, 2.00, '2025-04-25', 'Étude des actes de succession', 1),
(34, 3, 12, 1.50, '2025-05-10', 'Réunion avec les héritiers', 1),
(35, 6, 12, 1.00, '2025-05-05', 'Numérisation des documents anciens', 0),
-- Me Leila - Dossier 13 (archivé)
(36, 3, 13, 2.00, '2025-02-05', 'Première consultation', 1),
(37, 3, 13, 3.00, '2025-03-01', 'Préparation de la défense', 1),
(38, 3, 13, 4.00, '2025-04-15', 'Audience au tribunal', 1),
-- Me Leila - Dossier 14 (clôturé)
(39, 3, 14, 2.00, '2025-05-22', 'Consultation création SARL', 1),
(40, 3, 14, 3.00, '2025-06-01', 'Rédaction des statuts', 1),
-- Me Leila - Dossier 15
(41, 3, 15, 3.00, '2025-05-05', 'Visite du chantier avec photos', 1),
(42, 3, 15, 2.00, '2025-05-15', 'Rédaction du rapport de malfaçons', 1),
(43, 3, 15, 1.50, '2025-06-01', 'Demande d\'expertise judiciaire', 1),
(44, 6, 15, 2.00, '2025-05-10', 'Tri des documents techniques', 0),
-- Me Leila - Dossier 16
(45, 3, 16, 1.50, '2025-06-02', 'Recherche disponibilité marque INNORPI', 1),
(46, 3, 16, 2.00, '2025-06-10', 'Préparation du dossier de dépôt', 1),
-- Me Leila - Dossier 17
(47, 3, 17, 2.50, '2025-06-16', 'Analyse du contrat de sous-traitance', 1),
(48, 3, 17, 1.50, '2025-06-20', 'Évaluation du préjudice', 1),
-- Me Leila - Dossier 18
(49, 3, 18, 1.50, '2025-06-21', 'Consultation pension alimentaire', 1),
(50, 3, 18, 2.00, '2025-06-23', 'Calcul de la nouvelle pension', 1),
-- Me Leila - Dossier 19
(51, 3, 19, 2.00, '2025-05-12', 'Étude du refus de permis', 1),
(52, 3, 19, 1.50, '2025-05-25', 'Préparation du recours', 1),
-- Me Leila - Dossier 20
(53, 3, 20, 2.00, '2025-06-19', 'Rédaction contrat de promotion', 1),
(54, 6, 20, 1.00, '2025-06-20', 'Recherches réglementaires', 0),

-- Me Karim Ayari - Dossier 21
(55, 4, 21, 3.00, '2025-05-02', 'Étude du dossier pénal', 1),
(56, 4, 21, 2.00, '2025-05-10', 'Audition avec le client', 1),
(57, 4, 21, 2.50, '2025-05-20', 'Préparation de la défense', 1),
(58, 4, 21, 1.50, '2025-06-01', 'Recherche de témoins', 1),
-- Me Karim - Dossier 22
(59, 4, 22, 1.50, '2025-05-16', 'Étude du bail et des loyers', 1),
(60, 4, 22, 1.00, '2025-05-25', 'Rédaction du commandement de payer', 1),
(61, 4, 22, 2.00, '2025-06-05', 'Signification par huissier', 1),
-- Me Karim - Dossier 23
(62, 4, 23, 2.00, '2025-06-02', 'Première consultation accident', 1),
(63, 4, 23, 1.50, '2025-06-10', 'Collecte des certificats médicaux', 1),
(64, 4, 23, 2.00, '2025-06-18', 'Préparation dossier CNAM', 1),
-- Me Karim - Dossier 24
(65, 4, 24, 3.00, '2025-05-22', 'Étude réglementation données personnelles', 1),
(66, 4, 24, 4.00, '2025-06-01', 'Rédaction des CGU', 1),
(67, 4, 24, 3.00, '2025-06-15', 'Rédaction du contrat SaaS', 1),
-- Me Karim - Dossier 25
(68, 4, 25, 2.00, '2025-06-06', 'Analyse du bail commercial', 1),
(69, 4, 25, 1.50, '2025-06-12', 'Négociation avec le bailleur', 1),
(70, 4, 25, 2.00, '2025-06-20', 'Préparation du mémoire', 1),
-- Me Karim - Dossier 26
(71, 4, 26, 3.00, '2025-06-13', 'Consultation du dossier d\'instruction', 1),
(72, 4, 26, 2.50, '2025-06-18', 'Recherche d\'éléments à décharge', 1),
-- Me Karim - Dossier 27
(73, 4, 27, 1.00, '2025-05-26', 'Consultation trouble de voisinage', 1),
(74, 4, 27, 1.50, '2025-06-05', 'Mise en demeure au propriétaire du café', 1),
-- Me Karim - Dossier 28
(75, 4, 28, 2.00, '2025-06-16', 'Audit préliminaire conformité', 1),
(76, 4, 28, 3.00, '2025-06-22', 'Rédaction des recommandations', 1),
-- Me Karim - Dossier 29 (clôturé)
(77, 4, 29, 3.00, '2025-04-05', 'Rédaction des modèles de contrats', 1),
(78, 4, 29, 2.00, '2025-05-10', 'Finalisation et livraison', 1),
-- Me Karim - Dossier 30
(79, 4, 30, 2.00, '2025-06-21', 'Première consultation harcèlement', 1),
(80, 4, 30, 1.50, '2025-06-23', 'Collecte des preuves', 1),

-- Entrées supplémentaires pour les assistants
(81, 5, 1, 0.50, '2025-03-18', 'Envoi courrier recommandé', 0),
(82, 5, 2, 1.00, '2025-04-12', 'Prise de rendez-vous expert', 0),
(83, 5, 3, 0.50, '2025-05-08', 'Appel téléphonique client', 0),
(84, 5, 7, 1.50, '2025-05-20', 'Recherches réglementaires', 0),
(85, 5, 8, 1.00, '2025-06-14', 'Photocopie et classement', 0),
(86, 5, 9, 0.50, '2025-06-17', 'Prise de rendez-vous notaire', 0),
(87, 6, 11, 0.50, '2025-04-18', 'Appel à l\'inspection du travail', 0),
(88, 6, 12, 1.00, '2025-04-28', 'Recherche documents anciens', 0),
(89, 6, 15, 0.50, '2025-05-08', 'Coordination visite chantier', 0),
(90, 6, 16, 1.00, '2025-06-05', 'Recherche INNORPI', 0),
(91, 6, 17, 0.50, '2025-06-18', 'Envoi de documents', 0),

-- Quelques entrées pour l'admin
(92, 1, 1, 0.50, '2025-04-01', 'Supervision du dossier', 0),
(93, 1, 8, 1.00, '2025-06-12', 'Revue du dossier fiscal', 0),
(94, 1, 11, 0.50, '2025-04-25', 'Point d\'avancement', 0),
(95, 1, 21, 0.50, '2025-05-15', 'Supervision dossier pénal', 0),

-- Entrées supplémentaires
(96, 2, 1, 1.50, '2025-04-15', 'Négociation avec la partie adverse', 1),
(97, 3, 12, 3.00, '2025-05-20', 'Audience préliminaire au tribunal', 1),
(98, 4, 22, 1.50, '2025-06-10', 'Suivi de la procédure d\'expulsion', 1),
(99, 3, 15, 2.50, '2025-06-10', 'Réunion avec l\'expert judiciaire', 1),
(100,4, 24, 2.00, '2025-06-20', 'Révision finale des CGU', 1);

-- ============================================================
-- FACTURES (35 factures avec articles)
-- ============================================================
INSERT INTO factures (id, client_id, dossier_id, numero_facture, montant_ht, tva, montant_ttc, status, date_emission, date_echeance) VALUES
-- Factures de Me Sami
(1,  1,  1,  'F-2025-0001', 750.00,  19.00, 892.50,  'Payée',      '2025-04-01', '2025-05-01'),
(2,  1,  1,  'F-2025-0002', 1200.00, 19.00, 1428.00, 'En attente', '2025-05-01', '2025-05-31'),
(3,  2,  2,  'F-2025-0003', 900.00,  19.00, 1071.00, 'Payée',      '2025-04-15', '2025-05-15'),
(4,  2,  2,  'F-2025-0004', 1500.00, 19.00, 1785.00, 'En attente', '2025-05-15', '2025-06-15'),
(5,  6,  3,  'F-2025-0005', 600.00,  19.00, 714.00,  'Payée',      '2025-05-10', '2025-06-10'),
(6,  9,  4,  'F-2025-0006', 800.00,  19.00, 952.00,  'Impayée',    '2025-05-01', '2025-05-31'),
(7,  12, 5,  'F-2025-0007', 2500.00, 19.00, 2975.00, 'Payée',      '2025-05-20', '2025-06-20'),
(8,  13, 6,  'F-2025-0008', 1800.00, 19.00, 2142.00, 'En attente', '2025-06-05', '2025-07-05'),
(9,  16, 7,  'F-2025-0009', 1200.00, 19.00, 1428.00, 'Brouillon',  '2025-06-01', NULL),
(10, 19, 8,  'F-2025-0010', 3500.00, 19.00, 4165.00, 'En attente', '2025-06-15', '2025-07-15'),
(11, 1,  9,  'F-2025-0011', 500.00,  19.00, 595.00,  'Brouillon',  '2025-06-20', NULL),
(12, 13, 10, 'F-2025-0012', 3000.00, 19.00, 3570.00, 'Payée',      '2025-05-01', '2025-06-01'),

-- Factures de Me Leila
(13, 3,  11, 'F-2025-0013', 1000.00, 19.00, 1190.00, 'Payée',      '2025-04-20', '2025-05-20'),
(14, 3,  11, 'F-2025-0014', 1500.00, 19.00, 1785.00, 'En attente', '2025-06-01', '2025-07-01'),
(15, 4,  12, 'F-2025-0015', 1200.00, 19.00, 1428.00, 'En attente', '2025-05-20', '2025-06-20'),
(16, 8,  13, 'F-2025-0016', 2200.00, 19.00, 2618.00, 'Payée',      '2025-04-20', '2025-05-20'),
(17, 11, 14, 'F-2025-0017', 800.00,  19.00, 952.00,  'Payée',      '2025-06-10', '2025-07-10'),
(18, 14, 15, 'F-2025-0018', 1800.00, 19.00, 2142.00, 'En attente', '2025-06-01', '2025-07-01'),
(19, 17, 16, 'F-2025-0019', 600.00,  19.00, 714.00,  'Brouillon',  '2025-06-15', NULL),
(20, 20, 17, 'F-2025-0020', 2000.00, 19.00, 2380.00, 'En attente', '2025-06-20', '2025-07-20'),
(21, 3,  18, 'F-2025-0021', 500.00,  19.00, 595.00,  'Brouillon',  '2025-06-22', NULL),
(22, 4,  19, 'F-2025-0022', 900.00,  19.00, 1071.00, 'Impayée',    '2025-05-20', '2025-06-20'),
(23, 14, 20, 'F-2025-0023', 1500.00, 19.00, 1785.00, 'Brouillon',  '2025-06-20', NULL),

-- Factures de Me Karim
(24, 5,  21, 'F-2025-0024', 2000.00, 19.00, 2380.00, 'En attente', '2025-06-01', '2025-07-01'),
(25, 7,  22, 'F-2025-0025', 800.00,  19.00, 952.00,  'Payée',      '2025-06-01', '2025-07-01'),
(26, 10, 23, 'F-2025-0026', 1000.00, 19.00, 1190.00, 'En attente', '2025-06-15', '2025-07-15'),
(27, 15, 24, 'F-2025-0027', 4000.00, 19.00, 4760.00, 'En attente', '2025-06-10', '2025-07-10'),
(28, 18, 25, 'F-2025-0028', 1500.00, 19.00, 1785.00, 'En attente', '2025-06-15', '2025-07-15'),
(29, 5,  26, 'F-2025-0029', 1800.00, 19.00, 2142.00, 'Brouillon',  '2025-06-20', NULL),
(30, 7,  27, 'F-2025-0030', 400.00,  19.00, 476.00,  'Annulée',    '2025-06-01', NULL),
(31, 15, 28, 'F-2025-0031', 2500.00, 19.00, 2975.00, 'Brouillon',  '2025-06-22', NULL),
(32, 18, 29, 'F-2025-0032', 1800.00, 19.00, 2142.00, 'Payée',      '2025-05-20', '2025-06-20'),
(33, 10, 30, 'F-2025-0033', 700.00,  19.00, 833.00,  'Brouillon',  '2025-06-23', NULL),

-- Factures supplémentaires
(34, 13, 6,  'F-2025-0034', 2200.00, 19.00, 2618.00, 'Brouillon', '2025-06-20', NULL),
(35, 16, 7,  'F-2025-0035', 1500.00, 19.00, 1785.00, 'En attente', '2025-06-20', '2025-07-20');

-- ============================================================
-- ARTICLES DE FACTURES
-- ============================================================
INSERT INTO article_facture (id, facture_id, description, quantite, prix_unitaire, total) VALUES
-- F-2025-0001
(1,  1,  'Consultation juridique initiale', 1.00, 200.00, 200.00),
(2,  1,  'Rédaction de mise en demeure', 1.00, 350.00, 350.00),
(3,  1,  'Frais de dossier', 1.00, 200.00, 200.00),
-- F-2025-0002
(4,  2,  'Visite des lieux et constat', 1.00, 300.00, 300.00),
(5,  2,  'Rédaction d\'assignation', 1.00, 500.00, 500.00),
(6,  2,  'Honoraires consultation (2h)', 2.00, 200.00, 400.00),
-- F-2025-0003
(7,  3,  'Consultation post-accident', 1.00, 250.00, 250.00),
(8,  3,  'Analyse du rapport de police', 1.00, 350.00, 350.00),
(9,  3,  'Frais administratifs', 1.00, 300.00, 300.00),
-- F-2025-0004
(10, 4,  'Expertise médicale - coordination', 1.00, 500.00, 500.00),
(11, 4,  'Rédaction demande d\'indemnisation', 1.00, 600.00, 600.00),
(12, 4,  'Honoraires de suivi (2h)', 2.00, 200.00, 400.00),
-- F-2025-0005
(13, 5,  'Consultation divorce amiable', 1.00, 300.00, 300.00),
(14, 5,  'Rédaction convention de divorce', 1.00, 300.00, 300.00),
-- F-2025-0006
(15, 6,  'Étude vices cachés immobilier', 1.00, 400.00, 400.00),
(16, 6,  'Visite terrain et constat', 1.00, 400.00, 400.00),
-- F-2025-0007
(17, 7,  'Procédure complète de recouvrement', 1.00, 1500.00, 1500.00),
(18, 7,  'Frais d\'huissier', 1.00, 500.00, 500.00),
(19, 7,  'Audience tribunal de commerce', 1.00, 500.00, 500.00),
-- F-2025-0008
(20, 8,  'Rédaction contrat de distribution', 1.00, 1200.00, 1200.00),
(21, 8,  'Conseil en droit commercial (3h)', 3.00, 200.00, 600.00),
-- F-2025-0009
(22, 9,  'Consultation agrément sanitaire', 1.00, 500.00, 500.00),
(23, 9,  'Préparation dossier administratif', 1.00, 700.00, 700.00),
-- F-2025-0010
(24, 10, 'Analyse redressement fiscal', 1.00, 1500.00, 1500.00),
(25, 10, 'Rédaction réclamation fiscale', 1.00, 1200.00, 1200.00),
(26, 10, 'Honoraires conseil fiscal (4h)', 4.00, 200.00, 800.00),
-- F-2025-0011
(27, 11, 'Consultation succession', 1.00, 300.00, 300.00),
(28, 11, 'Frais préliminaires', 1.00, 200.00, 200.00),
-- F-2025-0012
(29, 12, 'Rédaction de 5 contrats de travail', 5.00, 400.00, 2000.00),
(30, 12, 'Conseil droit du travail (5h)', 5.00, 200.00, 1000.00),
-- F-2025-0013
(31, 13, 'Consultation licenciement abusif', 1.00, 300.00, 300.00),
(32, 13, 'Étude du dossier salarial', 1.00, 400.00, 400.00),
(33, 13, 'Frais administratifs', 1.00, 300.00, 300.00),
-- F-2025-0014
(34, 14, 'Recherche jurisprudence', 1.00, 500.00, 500.00),
(35, 14, 'Préparation conclusions (5h)', 5.00, 200.00, 1000.00),
-- F-2025-0015
(36, 15, 'Consultation registre foncier', 1.00, 400.00, 400.00),
(37, 15, 'Étude actes de succession', 1.00, 500.00, 500.00),
(38, 15, 'Frais de déplacement Bizerte', 1.00, 300.00, 300.00),
-- F-2025-0016
(39, 16, 'Défense diffamation - dossier complet', 1.00, 1500.00, 1500.00),
(40, 16, 'Audience tribunal (2 audiences)', 2.00, 350.00, 700.00),
-- F-2025-0017
(41, 17, 'Conseil création SARL', 1.00, 400.00, 400.00),
(42, 17, 'Rédaction statuts SARL', 1.00, 400.00, 400.00),
-- F-2025-0018
(43, 18, 'Visite chantier et expertise', 1.00, 600.00, 600.00),
(44, 18, 'Rédaction rapport malfaçons', 1.00, 700.00, 700.00),
(45, 18, 'Demande expertise judiciaire', 1.00, 500.00, 500.00),
-- F-2025-0019
(46, 19, 'Recherche INNORPI', 1.00, 200.00, 200.00),
(47, 19, 'Dossier dépôt de marque', 1.00, 400.00, 400.00),
-- F-2025-0020
(48, 20, 'Analyse contrat sous-traitance', 1.00, 800.00, 800.00),
(49, 20, 'Évaluation préjudice financier', 1.00, 700.00, 700.00),
(50, 20, 'Honoraires conseil (2.5h)', 2.50, 200.00, 500.00),
-- F-2025-0021
(51, 21, 'Consultation pension alimentaire', 1.00, 300.00, 300.00),
(52, 21, 'Calcul pension révisée', 1.00, 200.00, 200.00),
-- F-2025-0022
(53, 22, 'Étude refus permis de construire', 1.00, 400.00, 400.00),
(54, 22, 'Préparation recours administratif', 1.00, 500.00, 500.00),
-- F-2025-0023
(55, 23, 'Rédaction contrat promotion immobilière', 1.00, 1000.00, 1000.00),
(56, 23, 'Conseil juridique (2.5h)', 2.50, 200.00, 500.00),
-- F-2025-0024
(57, 24, 'Défense pénale - agression', 1.00, 1200.00, 1200.00),
(58, 24, 'Audiences préliminaires', 2.00, 400.00, 800.00),
-- F-2025-0025
(59, 25, 'Procédure d\'expulsion', 1.00, 500.00, 500.00),
(60, 25, 'Frais d\'huissier', 1.00, 300.00, 300.00),
-- F-2025-0026
(61, 26, 'Consultation accident travail', 1.00, 300.00, 300.00),
(62, 26, 'Dossier CNAM', 1.00, 400.00, 400.00),
(63, 26, 'Expertise IPP', 1.00, 300.00, 300.00),
-- F-2025-0027
(64, 27, 'Rédaction CGU plateforme', 1.00, 1500.00, 1500.00),
(65, 27, 'Rédaction contrat SaaS', 1.00, 1500.00, 1500.00),
(66, 27, 'Conseil RGPD (5h)', 5.00, 200.00, 1000.00),
-- F-2025-0028
(67, 28, 'Analyse bail commercial', 1.00, 500.00, 500.00),
(68, 28, 'Négociation et médiation', 1.00, 600.00, 600.00),
(69, 28, 'Honoraires conseil (2h)', 2.00, 200.00, 400.00),
-- F-2025-0029
(70, 29, 'Défense pénale - vol', 1.00, 1200.00, 1200.00),
(71, 29, 'Consultation dossier instruction', 1.00, 600.00, 600.00),
-- F-2025-0030
(72, 30, 'Mise en demeure voisinage', 1.00, 200.00, 200.00),
(73, 30, 'Consultation juridique', 1.00, 200.00, 200.00),
-- F-2025-0031
(74, 31, 'Audit conformité données', 1.00, 1500.00, 1500.00),
(75, 31, 'Recommandations RGPD', 1.00, 1000.00, 1000.00),
-- F-2025-0032
(76, 32, 'Rédaction modèles contrats location', 5.00, 300.00, 1500.00),
(77, 32, 'Frais de conseil', 1.00, 300.00, 300.00),
-- F-2025-0033
(78, 33, 'Consultation harcèlement moral', 1.00, 400.00, 400.00),
(79, 33, 'Frais préliminaires', 1.00, 300.00, 300.00),
-- F-2025-0034
(80, 34, 'Révision contrat distribution', 1.00, 1200.00, 1200.00),
(81, 34, 'Négociation clauses (5h)', 5.00, 200.00, 1000.00),
-- F-2025-0035
(82, 35, 'Suivi dossier agrément', 1.00, 800.00, 800.00),
(83, 35, 'Honoraires conseil (3.5h)', 3.50, 200.00, 700.00);

-- ============================================================
-- DOCUMENTS (50 documents mock)
-- ============================================================
INSERT INTO document (id, dossier_id, telechargepar_id, titre, chemin_fichier, nom_original, type, confidentialite, created_at) VALUES
-- Dossier 1
(1,  1, 2, 'Contrat de bail', 'uploads/documents/contrat_bail_001.pdf', 'contrat_bail.pdf', 'Contrat', 'Confidentiel', '2025-03-16 10:00:00'),
(2,  1, 5, 'Relevés de loyer impayés', 'uploads/documents/releves_loyer_001.pdf', 'releves_loyer.pdf', 'Justificatif', 'Interne', '2025-03-18 11:00:00'),
(3,  1, 2, 'Mise en demeure signée', 'uploads/documents/mise_en_demeure_001.pdf', 'mise_en_demeure.pdf', 'Correspondance', 'Confidentiel', '2025-04-02 14:00:00'),
-- Dossier 2
(4,  2, 2, 'Rapport de police', 'uploads/documents/rapport_police_002.pdf', 'rapport_police.pdf', 'Justificatif', 'Secret', '2025-04-05 09:00:00'),
(5,  2, 5, 'Certificat médical initial', 'uploads/documents/certificat_medical_002.pdf', 'certificat_medical.pdf', 'Justificatif', 'Secret', '2025-04-10 10:00:00'),
(6,  2, 2, 'Photos du véhicule accidenté', 'uploads/documents/photos_accident_002.pdf', 'photos_accident.pdf', 'Autre', 'Interne', '2025-04-12 11:00:00'),
-- Dossier 3
(7,  3, 2, 'Projet de convention de divorce', 'uploads/documents/convention_divorce_003.pdf', 'convention_divorce.pdf', 'Contrat', 'Secret', '2025-05-10 10:00:00'),
-- Dossier 5
(8,  5, 2, 'Assignation tribunal de commerce', 'uploads/documents/assignation_005.pdf', 'assignation.pdf', 'Plainte', 'Confidentiel', '2025-03-10 09:00:00'),
(9,  5, 2, 'Jugement rendu', 'uploads/documents/jugement_005.pdf', 'jugement.pdf', 'Jugement', 'Confidentiel', '2025-05-15 14:00:00'),
-- Dossier 6
(10, 6, 2, 'Projet de contrat distribution', 'uploads/documents/contrat_distribution_006.pdf', 'contrat_distribution.pdf', 'Contrat', 'Confidentiel', '2025-06-10 10:00:00'),
-- Dossier 7
(11, 7, 5, 'Dossier agrément sanitaire', 'uploads/documents/agrement_007.pdf', 'dossier_agrement.pdf', 'Justificatif', 'Interne', '2025-05-20 11:00:00'),
-- Dossier 8
(12, 8, 2, 'Avis de redressement fiscal', 'uploads/documents/redressement_008.pdf', 'avis_redressement.pdf', 'Correspondance', 'Secret', '2025-06-11 09:00:00'),
(13, 8, 5, 'Déclarations fiscales', 'uploads/documents/declarations_008.pdf', 'declarations_fiscales.pdf', 'Justificatif', 'Secret', '2025-06-13 10:00:00'),
(14, 8, 2, 'Réclamation fiscale', 'uploads/documents/reclamation_008.pdf', 'reclamation.pdf', 'Correspondance', 'Confidentiel', '2025-06-15 14:00:00'),
-- Dossier 11
(15, 11, 3, 'Contrat de travail de l\'employé', 'uploads/documents/contrat_travail_011.pdf', 'contrat_travail.pdf', 'Contrat', 'Confidentiel', '2025-04-06 10:00:00'),
(16, 11, 6, 'Bulletins de paie', 'uploads/documents/bulletins_paie_011.pdf', 'bulletins_paie.pdf', 'Justificatif', 'Secret', '2025-04-20 11:00:00'),
(17, 11, 3, 'Lettre de licenciement', 'uploads/documents/licenciement_011.pdf', 'lettre_licenciement.pdf', 'Correspondance', 'Confidentiel', '2025-04-08 09:00:00'),
-- Dossier 12
(18, 12, 3, 'Titre de propriété du terrain', 'uploads/documents/titre_propriete_012.pdf', 'titre_propriete.pdf', 'Justificatif', 'Secret', '2025-04-16 14:00:00'),
(19, 12, 6, 'Acte de succession', 'uploads/documents/acte_succession_012.pdf', 'acte_succession.pdf', 'Justificatif', 'Secret', '2025-05-05 10:00:00'),
-- Dossier 13
(20, 13, 3, 'Plainte pour diffamation', 'uploads/documents/plainte_013.pdf', 'plainte_diffamation.pdf', 'Plainte', 'Confidentiel', '2025-02-05 09:00:00'),
(21, 13, 3, 'Jugement du tribunal', 'uploads/documents/jugement_013.pdf', 'jugement.pdf', 'Jugement', 'Public', '2025-04-30 15:00:00'),
-- Dossier 14
(22, 14, 3, 'Statuts de la SARL', 'uploads/documents/statuts_014.pdf', 'statuts_sarl.pdf', 'Contrat', 'Confidentiel', '2025-06-01 10:00:00'),
-- Dossier 15
(23, 15, 3, 'Photos des malfaçons', 'uploads/documents/photos_malfacons_015.pdf', 'photos_malfacons.pdf', 'Autre', 'Interne', '2025-05-05 10:00:00'),
(24, 15, 6, 'Rapport technique initial', 'uploads/documents/rapport_technique_015.pdf', 'rapport_technique.pdf', 'Justificatif', 'Confidentiel', '2025-05-10 14:00:00'),
(25, 15, 3, 'Demande d\'expertise judiciaire', 'uploads/documents/expertise_015.pdf', 'demande_expertise.pdf', 'Correspondance', 'Confidentiel', '2025-06-01 09:00:00'),
-- Dossier 17
(26, 17, 3, 'Contrat de sous-traitance', 'uploads/documents/contrat_soustraitance_017.pdf', 'contrat_soustraitance.pdf', 'Contrat', 'Confidentiel', '2025-06-16 10:00:00'),
-- Dossier 21
(27, 21, 4, 'PV d\'audition', 'uploads/documents/pv_audition_021.pdf', 'pv_audition.pdf', 'Justificatif', 'Secret', '2025-05-02 14:00:00'),
(28, 21, 4, 'Certificat médical victime', 'uploads/documents/certificat_victime_021.pdf', 'certificat_medical_victime.pdf', 'Justificatif', 'Secret', '2025-05-05 10:00:00'),
-- Dossier 22
(29, 22, 4, 'Bail locatif', 'uploads/documents/bail_022.pdf', 'bail_locatif.pdf', 'Contrat', 'Confidentiel', '2025-05-16 09:00:00'),
(30, 22, 4, 'Commandement de payer', 'uploads/documents/commandement_022.pdf', 'commandement_payer.pdf', 'Correspondance', 'Confidentiel', '2025-06-05 11:00:00'),
-- Dossier 23
(31, 23, 4, 'Certificat médical accident travail', 'uploads/documents/certificat_at_023.pdf', 'certificat_accident.pdf', 'Justificatif', 'Secret', '2025-06-02 10:00:00'),
(32, 23, 4, 'Déclaration accident CNAM', 'uploads/documents/declaration_cnam_023.pdf', 'declaration_cnam.pdf', 'Justificatif', 'Confidentiel', '2025-06-10 14:00:00'),
-- Dossier 24
(33, 24, 4, 'Brouillon CGU', 'uploads/documents/cgu_024.pdf', 'cgu_brouillon.pdf', 'Contrat', 'Interne', '2025-06-01 10:00:00'),
(34, 24, 4, 'Contrat SaaS brouillon', 'uploads/documents/saas_024.pdf', 'contrat_saas.pdf', 'Contrat', 'Confidentiel', '2025-06-15 14:00:00'),
-- Dossier 25
(35, 25, 4, 'Bail commercial Khmiri', 'uploads/documents/bail_commercial_025.pdf', 'bail_commercial.pdf', 'Contrat', 'Confidentiel', '2025-06-06 09:00:00'),
-- Dossier 26
(36, 26, 4, 'Dossier d\'instruction', 'uploads/documents/instruction_026.pdf', 'dossier_instruction.pdf', 'Justificatif', 'Secret', '2025-06-13 10:00:00'),
-- Dossier 28
(37, 28, 4, 'Rapport audit RGPD', 'uploads/documents/audit_rgpd_028.pdf', 'audit_rgpd.pdf', 'Autre', 'Secret', '2025-06-16 14:00:00'),
-- Dossier 29
(38, 29, 4, 'Modèles de contrats location', 'uploads/documents/modeles_location_029.pdf', 'modeles_location.pdf', 'Contrat', 'Interne', '2025-05-10 10:00:00'),
-- Dossier 30
(39, 30, 4, 'Témoignage collègue 1', 'uploads/documents/temoignage1_030.pdf', 'temoignage_1.pdf', 'Justificatif', 'Secret', '2025-06-21 10:00:00'),
(40, 30, 4, 'Certificats arrêt maladie', 'uploads/documents/arret_maladie_030.pdf', 'arret_maladie.pdf', 'Justificatif', 'Secret', '2025-06-22 11:00:00'),

-- Documents supplémentaires
(41, 4,  2, 'Procuration signée', 'uploads/documents/procuration_004.pdf', 'procuration.pdf', 'Procuration', 'Confidentiel', '2025-04-25 10:00:00'),
(42, 9,  2, 'Acte de décès', 'uploads/documents/acte_deces_009.pdf', 'acte_deces.pdf', 'Justificatif', 'Secret', '2025-06-16 09:00:00'),
(43, 10, 2, 'Contrats de travail signés', 'uploads/documents/contrats_signes_010.pdf', 'contrats_signes.pdf', 'Contrat', 'Confidentiel', '2025-04-30 14:00:00'),
(44, 16, 3, 'Formulaire INNORPI', 'uploads/documents/innorpi_016.pdf', 'formulaire_innorpi.pdf', 'Justificatif', 'Interne', '2025-06-05 10:00:00'),
(45, 18, 3, 'Bulletins de paie époux', 'uploads/documents/paie_018.pdf', 'bulletins_paie_epoux.pdf', 'Justificatif', 'Secret', '2025-06-21 11:00:00'),
(46, 19, 3, 'Arrêté de refus permis', 'uploads/documents/refus_permis_019.pdf', 'arrete_refus.pdf', 'Correspondance', 'Confidentiel', '2025-05-12 09:00:00'),
(47, 20, 3, 'Cahier des charges projet', 'uploads/documents/cahier_charges_020.pdf', 'cahier_charges.pdf', 'Autre', 'Interne', '2025-06-19 10:00:00'),
(48, 27, 4, 'Constat de nuisances', 'uploads/documents/constat_nuisances_027.pdf', 'constat_nuisances.pdf', 'Justificatif', 'Interne', '2025-05-26 14:00:00'),
(49, 2,  5, 'Rapport expert automobile', 'uploads/documents/expert_auto_002.pdf', 'rapport_expert_auto.pdf', 'Justificatif', 'Confidentiel', '2025-04-18 11:00:00'),
(50, 6,  5, 'Note juridique concurrence', 'uploads/documents/note_concurrence_006.pdf', 'note_concurrence.pdf', 'Autre', 'Interne', '2025-06-08 10:00:00');

-- ============================================================
-- NOTIFICATIONS (20 notifications)
-- ============================================================
INSERT INTO notification (id, message, type, created_at, is_read, link, user_id) VALUES
-- Pour Admin
(1,  'Nouveau dossier créé: Litige fiscal - Redressement', 'dossier', '2025-06-10 10:00:00', 1, '/dossier/8', 1),
(2,  'Facture F-2025-0010 en attente de validation (3 500 DT)', 'facture', '2025-06-15 11:00:00', 0, '/factures/10', 1),
(3,  'Nouveau client entreprise ajouté: Hôtel Hannibal Palace', 'client', '2025-06-15 11:30:00', 0, '/client/20', 1),

-- Pour Me Sami Trabelsi
(4,  'Tâche urgente: Déposer le recours administratif (Dossier fiscal)', 'tache', '2025-06-20 09:00:00', 0, '/tache/13', 2),
(5,  'Rendez-vous confirmé avec Ahmed Mathlouthi le 01/07/2026', 'rendezvous', '2025-06-23 08:00:00', 0, '/rendezvous/1', 2),
(6,  'Facture F-2025-0006 impayée depuis 30 jours', 'facture', '2025-06-30 09:00:00', 0, '/factures/6', 2),
(7,  'Nouveau document ajouté au dossier: Litige locatif', 'document', '2025-04-02 14:30:00', 1, '/dossier/1', 2),

-- Pour Me Leila Gharbi
(8,  'Tâche urgente: Engager la procédure judiciaire (Héritage Bizerte)', 'tache', '2025-06-20 09:00:00', 0, '/tache/19', 3),
(9,  'Facture F-2025-0022 impayée: Permis de construire refusé', 'facture', '2025-06-20 10:00:00', 0, '/factures/22', 3),
(10, 'Rendez-vous avec Jaziri Construction le 07/07/2026', 'rendezvous', '2025-06-23 08:30:00', 0, '/rendezvous/5', 3),
(11, 'Dossier archivé: Défense pénale - Diffamation', 'dossier', '2025-05-01 15:00:00', 1, '/dossier/13', 3),

-- Pour Me Karim Ayari
(12, 'Tâche urgente: Plaider devant le tribunal (Agression physique)', 'tache', '2025-06-20 09:00:00', 0, '/tache/27', 4),
(13, 'Nouveau dossier: Harcèlement moral au travail', 'dossier', '2025-06-20 10:00:00', 0, '/dossier/30', 4),
(14, 'Rendez-vous en attente avec Salah Cherif le 05/07/2026', 'rendezvous', '2025-06-23 08:00:00', 0, '/rendezvous/4', 4),

-- Pour Maram Jaziri (assistante)
(15, 'Nouvelle tâche assignée: Rassembler les preuves de loyers impayés', 'tache', '2025-04-01 09:00:00', 1, '/tache/2', 5),
(16, 'Nouvelle tâche assignée: Préparer le dossier de réclamation fiscale', 'tache', '2025-06-12 10:00:00', 0, '/tache/12', 5),
(17, 'Rappel: Tâche en retard - Préparer le dossier administratif (agrément)', 'tache', '2025-06-20 09:00:00', 0, '/tache/43', 5),

-- Pour Nour Mabrouk (assistante)
(18, 'Nouvelle tâche assignée: Collecter les témoignages (Licenciement)', 'tache', '2025-04-10 09:00:00', 1, '/tache/15', 6),
(19, 'Nouvelle tâche assignée: Photographier les malfaçons', 'tache', '2025-05-01 10:00:00', 1, '/tache/21', 6),
(20, 'Rappel: Tâche en cours - Calculer le préjudice financier', 'tache', '2025-06-20 09:00:00', 0, '/tache/24', 6);
