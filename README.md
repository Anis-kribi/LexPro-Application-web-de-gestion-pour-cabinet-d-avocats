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
<img width="1271" height="639" alt="image" src="https://github.com/user-attachments/assets/82ee89c4-45b5-4437-bd5e-53c814b7e388" />


**Tableau de Bord Principal**
<img width="1275" height="643" alt="image" src="https://github.com/user-attachments/assets/757bf5ec-593f-4147-8eb2-9a8d6f61da2c" />


**Gestion des Dossiers et Vues Détaillées**
<img width="1276" height="618" alt="image" src="https://github.com/user-attachments/assets/80f3fb71-5928-49ea-b190-928447b44d80" />

<img width="1271" height="614" alt="image" src="https://github.com/user-attachments/assets/d1e43bee-c48d-47d6-b87c-75c05e245c92" />

**Saisie des Entrées de Temps**
<img width="1277" height="617" alt="image" src="https://github.com/user-attachments/assets/c3031232-2b0a-4207-8ead-0907b7bbcde8" />


**Facturation et Rendu PDF**
<img width="1274" height="613" alt="image" src="https://github.com/user-attachments/assets/469a83d6-004a-41f3-9c0f-87bf0b5d6af9" />


**Calendrier des Rendez-vous**

<img width="1276" height="613" alt="image" src="https://github.com/user-attachments/assets/0018436b-33d1-4e4e-81f0-314f18cd98f2" />

<img width="1269" height="613" alt="image" src="https://github.com/user-attachments/assets/405b1ce7-f2f5-4b7f-8302-8828683a7bd1" />

## 🛡️ Sécurité

Le secret professionnel étant fondamental, LexPro intègre une authentification avec hachage de mot de passe `bcrypt`, une protection native CSRF via Symfony, et une validation stricte des données pour se prémunir des failles (notamment protection XSS automatique grâce à Twig).
