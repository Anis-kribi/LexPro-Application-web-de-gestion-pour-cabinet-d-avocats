# LexPro - Plateforme de Gestion pour Cabinet d'Avocats

LexPro (Legal Professional) est une application web sur mesure centralisée, développée pour simplifier et optimiser la gestion quotidienne des cabinets d'avocats. Conçue pour répondre aux exigences de sécurité et d'efficacité du domaine juridique, elle permet de consolider les dossiers, d'automatiser les tâches chronophages et de piloter l'activité via un accès sécurisé basé sur les rôles.

## 🚀 Fonctionnalités Principales

* **Tableau de bord dynamique :** Synthèse visuelle en temps réel incluant les indicateurs de performance (KPIs) tels que les dossiers actifs, le chiffre d'affaires du mois et les tâches urgentes.
* **Gestion des dossiers juridiques :** Centralisation complète de toutes les informations d'une affaire (parties prenantes, historique, tâches, documents) pour éviter la dispersion des données.
* **Suivi du temps (Time-Tracking) :** Enregistrement précis des heures de travail (rédaction, audiences, recherches) afin de garantir une facturation exhaustive.
* **Facturation et Export PDF :** Génération automatisée des factures avec calcul des montants (HT, TVA, TTC) et création de documents professionnels téléchargeables via Dompdf.
* **Agenda et Calendrier Interactif :** Planification des rendez-vous avec vues mensuelle, hebdomadaire et journalière via la bibliothèque FullCalendar.
* **Gestion Documentaire :** Upload sécurisé, organisation et classement des fichiers numériques (PDF, Word, Excel) liés aux dossiers.
* **Gestion des Clients :** Suivi centralisé et détaillé de la clientèle (particuliers et entreprises) avec historique complet.
* **Gestion des Tâches :** Suivi des actions à réaliser, des échéances et des priorités avec des indicateurs visuels clairs.

## 👥 Profils et Accès (RBAC)

L'application utilise un système granulaire de contrôle d'accès basé sur les rôles (Role-Based Access Control) via les Security Voters de Symfony.

* **Administrateur Système :** Dispose d'un accès complet pour gérer les comptes utilisateurs, configurer l'application et superviser l'activité globale.
* **Avocat (Associé) :** Gère l'intégralité du cycle de vie de ses dossiers, de la planification des audiences à l'enregistrement des temps et la facturation.
* **Assistant(e) Juridique :** Assure le support administratif (rendez-vous, classement de documents, saisie de clients) avec un périmètre d'action délimité par l'avocat associé.

## 💻 Stack Technologique

Le projet repose sur une architecture MVC stricte garantissant séparation des responsabilités, sécurité et maintenabilité.

| Composant | Technologie Utilisée |
| :--- | :--- |
| **Langage Back-end** | PHP 8.2 |
| **Framework Back-end** | Symfony 7.x |
| **Base de données** | MySQL 8 / MariaDB |
| **ORM** | Doctrine ORM |
| **Moteur de templates** | Twig |
| **Front-end** | Bootstrap 5, Bootstrap Icons |
| **Bibliothèques JS** | FullCalendar 6 |
| **Génération PDF** | Dompdf |

## 📸 Aperçu de l'Application


**Interface d'Authentification**
<img width="1271" height="639" alt="image" src="https://github.com/user-attachments/assets/a1068fcb-47f7-41e5-99a9-e80f1020e7fc" />


**Tableau de Bord Principal**
<img width="1275" height="643" alt="image" src="https://github.com/user-attachments/assets/ea5d66ea-d304-4cdd-b201-d69ae027cb2c" />


**Gestion des Dossiers et Vues Détaillées**
<img width="1276" height="618" alt="image" src="https://github.com/user-attachments/assets/4d6f7315-e9ba-4032-996f-fe517470c742" />

<img width="1271" height="614" alt="image" src="https://github.com/user-attachments/assets/977bfa42-92ac-43b1-a5c0-1f3d9b44f942" />

**Saisie des Entrées de Temps**
<img width="1277" height="617" alt="image" src="https://github.com/user-attachments/assets/e6b41b18-9270-4c16-a772-d75a26a1c1a8" />


**Facturation et Rendu PDF**
<img width="1274" height="613" alt="image" src="https://github.com/user-attachments/assets/71b4f532-5628-4308-8f9f-b2750b6ded9b" />


**Calendrier des Rendez-vous**

<img width="1276" height="613" alt="image" src="https://github.com/user-attachments/assets/e3eac871-cfa6-453a-aa13-ed88a847c977" />

<img width="1269" height="613" alt="image" src="https://github.com/user-attachments/assets/5a4fba70-3179-439d-a0b3-ebb91cd99bf7" />

## 🛡️ Sécurité

Le secret professionnel étant fondamental, LexPro intègre une authentification avec hachage de mot de passe `bcrypt`, une protection native CSRF via Symfony, et une validation stricte des données pour se prémunir des failles (notamment protection XSS automatique grâce à Twig).
