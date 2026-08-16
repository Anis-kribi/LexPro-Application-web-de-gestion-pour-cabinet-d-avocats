# ⚖️ LexPro — Plateforme de Gestion pour Cabinet d'Avocats

> **LexPro (Legal Professional)** est une application web de gestion conçue pour centraliser et simplifier les opérations quotidiennes d'un cabinet d'avocats.

L'application réunit au sein d'une même plateforme la **gestion des dossiers juridiques, des clients, des tâches, du temps de travail, des rendez-vous, des documents et de la facturation**, avec un système de contrôle d'accès basé sur les rôles.

L'objectif de LexPro est de réduire la dispersion des informations, améliorer le suivi des dossiers et fournir aux différents utilisateurs un environnement de travail **centralisé, structuré et sécurisé**.

---

## 🎯 Objectifs du projet

LexPro a été conçu autour de plusieurs problématiques liées à la gestion quotidienne d'un cabinet d'avocats :

* 📁 **Centraliser** les informations liées aux dossiers
* 👥 **Organiser** les données des clients et des parties prenantes
* ⏱️ **Suivre** précisément le temps consacré aux activités juridiques
* 💰 **Faciliter** la facturation et le suivi des revenus
* 📅 **Centraliser** les rendez-vous et les échéances
* 📄 **Organiser** les documents associés aux dossiers
* ✅ **Suivre** les tâches, priorités et délais
* 🔐 **Contrôler** l'accès aux fonctionnalités selon les responsabilités

---

# 🚀 Fonctionnalités

## 📊 Tableau de bord

Une interface centrale permettant d'obtenir rapidement une vue globale de l'activité du cabinet.

Le tableau de bord présente notamment :

* 📁 Nombre de dossiers actifs
* 💰 Chiffre d'affaires du mois
* ⚠️ Tâches urgentes
* 📈 Indicateurs clés de performance (**KPIs**)
* 📅 Informations importantes liées à l'activité

L'objectif est de permettre aux utilisateurs d'identifier rapidement les informations nécessitant leur attention.

---

## ⚖️ Gestion des dossiers juridiques

LexPro centralise les informations nécessaires au suivi d'une affaire dans un espace unique.

Chaque dossier peut regrouper :

* 👥 Parties prenantes
* 📋 Informations générales de l'affaire
* 🕒 Historique des activités
* ✅ Tâches associées
* 📄 Documents
* ⏱️ Temps enregistré
* 💰 Informations liées à la facturation

Cette organisation permet d'éviter la dispersion des données entre plusieurs outils.

---

## ⏱️ Time Tracking

Le module de suivi du temps permet d'enregistrer précisément les activités réalisées dans le cadre d'un dossier.

Exemples d'activités :

* ✍️ Rédaction
* ⚖️ Audiences
* 🔎 Recherches
* 📞 Autres activités professionnelles

Le temps enregistré peut ensuite être utilisé comme base pour la facturation.

---

## 💰 Facturation & Export PDF

LexPro intègre un système de facturation permettant de générer des documents professionnels à partir des prestations enregistrées.

Le système prend en charge :

* Calcul du montant **HT**
* Calcul de la **TVA**
* Calcul du montant **TTC**
* Génération des factures
* Export au format **PDF**
* Téléchargement des documents générés

La génération des documents PDF est réalisée avec **Dompdf**.

---

## 📅 Agenda & Calendrier interactif

Un calendrier interactif permet de centraliser les rendez-vous et les échéances du cabinet.

Le module propose plusieurs vues :

* 📆 Mensuelle
* 📅 Hebdomadaire
* 🗓️ Journalière

L'interface utilise **FullCalendar** pour faciliter la navigation et la visualisation des événements.

---

## 📄 Gestion documentaire

Les documents associés aux dossiers peuvent être téléversés et organisés directement depuis l'application.

Le système prend notamment en charge :

* 📥 Upload de documents
* 📁 Organisation et classement des fichiers
* 🔗 Association des documents aux dossiers
* 📄 Fichiers PDF
* 📝 Fichiers Word
* 📊 Fichiers Excel

La gestion documentaire permet de conserver les pièces importantes au même endroit que les informations du dossier concerné.

---

## 👥 Gestion des clients

LexPro permet de centraliser les informations relatives à la clientèle du cabinet.

Le système prend notamment en charge :

* 👤 Clients particuliers
* 🏢 Clients entreprises
* 📋 Informations détaillées
* 📁 Historique associé aux dossiers

---

## ✅ Gestion des tâches

Le module de tâches permet de suivre les actions à réaliser et les échéances associées aux dossiers.

Chaque tâche peut notamment contenir :

* 📝 Description
* 📅 Échéance
* 🚦 Priorité
* 📊 État d'avancement
* 🔗 Dossier associé

Des indicateurs visuels permettent d'identifier rapidement les tâches prioritaires ou urgentes.

---

# 👥 Rôles & contrôle d'accès

LexPro utilise un système de **Role-Based Access Control (RBAC)** afin d'adapter les permissions aux responsabilités de chaque utilisateur.

Le contrôle d'accès est implémenté à l'aide des **Security Voters de Symfony**.

## 🛡️ Administrateur système

L'administrateur dispose d'un accès global permettant notamment de :

* Gérer les comptes utilisateurs
* Configurer l'application
* Superviser l'activité
* Administrer les différentes ressources

## ⚖️ Avocat / Associé

L'avocat peut gérer le cycle de vie de ses dossiers, notamment :

* Gestion des affaires
* Planification des audiences
* Suivi du temps
* Gestion des clients
* Gestion documentaire
* Facturation

## 👩‍💼 Assistant(e) juridique

L'assistant(e) intervient sur les opérations administratives autorisées par son niveau de permission :

* Gestion des rendez-vous
* Classement des documents
* Saisie et gestion des clients
* Support administratif des dossiers

> Les permissions sont contrôlées côté serveur afin de limiter l'accès aux ressources selon le rôle et les droits de l'utilisateur.

---

# 🏗️ Architecture

LexPro repose sur une architecture **MVC (Model-View-Controller)** afin de séparer clairement les responsabilités de l'application.

```text
                    ┌─────────────────────┐
                    │       Browser       │
                    └──────────┬──────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │       Symfony       │
                    │      Controller     │
                    └──────────┬──────────┘
                               │
                 ┌─────────────┼─────────────┐
                 ▼             ▼             ▼
          ┌────────────┐ ┌────────────┐ ┌────────────┐
          │   Entity   │ │  Security  │ │  Services  │
          │  / Model   │ │   Voters   │ │ / Business │
          └──────┬─────┘ └────────────┘ └────────────┘
                 │
                 ▼
          ┌────────────────┐
          │   Doctrine ORM │
          └───────┬────────┘
                  │
                  ▼
          ┌────────────────┐
          │ MySQL / MariaDB│
          └────────────────┘
```

Cette architecture permet notamment de favoriser :

* ✅ Séparation des responsabilités
* ✅ Maintenabilité
* ✅ Organisation du code
* ✅ Réutilisation des composants
* ✅ Évolution progressive de l'application
* ✅ Gestion centralisée des règles métier et des permissions

---

# 🛠️ Stack technologique

| Domaine                       | Technologie             |
| :---------------------------- | :---------------------- |
| **Langage Backend**           | PHP 8.2                 |
| **Framework Backend**         | Symfony 7.x             |
| **Architecture**              | MVC                     |
| **Base de données**           | MySQL 8 / MariaDB       |
| **ORM**                       | Doctrine ORM            |
| **Moteur de templates**       | Twig                    |
| **Frontend**                  | Bootstrap 5             |
| **Icônes**                    | Bootstrap Icons         |
| **Calendrier**                | FullCalendar 6          |
| **Génération PDF**            | Dompdf                  |
| **Contrôle d'accès**          | Symfony Security Voters |
| **Protection CSRF**           | Symfony Security        |
| **Hashage des mots de passe** | bcrypt                  |

---

# 📸 Aperçu de l'application

## 🔐 Interface d'authentification

Interface de connexion permettant aux utilisateurs d'accéder à la plateforme selon leurs permissions.

<img width="1271" height="639" alt="LexPro - Interface d'authentification" src="https://github.com/user-attachments/assets/a1068fcb-47f7-41e5-99a9-e80f1020e7fc" />

---

## 📊 Tableau de bord principal

Vue globale de l'activité du cabinet et de ses principaux indicateurs.

<img width="1275" height="643" alt="LexPro - Tableau de bord principal" src="https://github.com/user-attachments/assets/ea5d66ea-d304-4cdd-b201-d69ae027cb2c" />

---

## ⚖️ Gestion des dossiers

Centralisation des dossiers juridiques et accès aux informations détaillées des affaires.

<img width="1276" height="618" alt="LexPro - Gestion des dossiers" src="https://github.com/user-attachments/assets/4d6f7315-e9ba-4032-996f-fe517470c742" />

<img width="1271" height="614" alt="LexPro - Vue détaillée d'un dossier" src="https://github.com/user-attachments/assets/977bfa42-92ac-43b1-a5c0-1f3d9b44f942" />

---

## ⏱️ Saisie des entrées de temps

Enregistrement des activités et du temps consacré aux dossiers.

<img width="1277" height="617" alt="LexPro - Time Tracking" src="https://github.com/user-attachments/assets/e6b41b18-9270-4c16-a772-d75a26a1c1a8" />

---

## 💰 Facturation & rendu PDF

Génération des factures et export des documents au format PDF.

<img width="1274" height="613" alt="LexPro - Facturation et PDF" src="https://github.com/user-attachments/assets/71b4f532-5628-4308-8f9f-b2750b6ded9b" />

---

## 📅 Calendrier des rendez-vous

Visualisation et planification des rendez-vous à travers le calendrier interactif.

<img width="1276" height="613" alt="LexPro - Calendrier" src="https://github.com/user-attachments/assets/e3eac871-cfa6-453-aa13-ed88a847c977" />

<img width="1269" height="613" alt="LexPro - Vue calendrier" src="https://github.com/user-attachments/assets/5a4fba70-3179-439d-a0b3-ebb91cd99bf7" />

---

# 🛡️ Sécurité

La confidentialité des informations est particulièrement importante dans une application destinée à la gestion de données juridiques.

LexPro intègre plusieurs mécanismes de sécurité au niveau de l'application :

### 🔐 Authentification

Les utilisateurs doivent s'authentifier avant d'accéder aux fonctionnalités protégées de la plateforme.

### 🔑 Hashage des mots de passe

Les mots de passe sont stockés sous forme **hachée avec bcrypt** et ne sont pas enregistrés en clair.

### 👥 Contrôle d'accès RBAC

Les **Security Voters de Symfony** permettent de vérifier les permissions avant l'accès aux ressources protégées.

### 🛡️ Protection CSRF

Symfony fournit une protection native contre les attaques **Cross-Site Request Forgery (CSRF)** sur les formulaires concernés.

### 🧹 Protection contre les injections XSS

Twig échappe automatiquement les variables affichées dans les templates lorsqu'il est utilisé avec son comportement par défaut, ce qui contribue à réduire les risques liés aux injections XSS.

### ✅ Validation des données

Les données reçues par l'application sont soumises à une validation afin de limiter les entrées invalides et incohérentes.

> ⚠️ **Note :** LexPro est un projet de démonstration / portfolio. Les mécanismes de sécurité présentés ne constituent pas une certification de sécurité ou de conformité juridique destinée à un environnement de production.

---

# 🚀 Installation

## Prérequis

Avant de lancer le projet, assurez-vous d'avoir installé :

* PHP **8.2+**
* Composer
* MySQL 8 ou MariaDB
* Symfony CLI *(recommandé)*
* Un environnement local compatible avec PHP

## 1. Cloner le projet

```bash
git clone https://github.com/Anis-kribi/LexPro.git
cd LexPro
```

## 2. Installer les dépendances

```bash
composer install
```

## 3. Configurer l'environnement

Configurez vos variables d'environnement selon votre installation locale, notamment la connexion à la base de données.

Exemple :

```env
DATABASE_URL="mysql://username:password@127.0.0.1:3306/lexpro"
```

## 4. Configurer la base de données

Selon la configuration du projet, créez et préparez la base de données avec Doctrine.

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

> Adaptez ces commandes à la structure réelle du projet si aucune migration n'est fournie dans le dépôt.

## 5. Lancer l'application

Avec Symfony CLI :

```bash
symfony server:start
```

Puis ouvrez l'adresse locale affichée par Symfony dans votre navigateur.

---

# 🔄 Flux fonctionnel

Le fonctionnement général de LexPro peut être résumé ainsi :

```text
                         ┌──────────────┐
                         │    Client    │
                         └──────┬───────┘
                                │
                                ▼
                         ┌──────────────┐
                         │Authentification│
                         └──────┬───────┘
                                │
                                ▼
                         ┌──────────────┐
                         │ Vérification │
                         │   du rôle    │
                         └──────┬───────┘
                                │
              ┌─────────────────┼─────────────────┐
              ▼                 ▼                 ▼
        ┌──────────┐      ┌──────────┐      ┌──────────┐
        │   Admin  │      │  Avocat  │      │ Assistant│
        └────┬─────┘      └────┬─────┘      └────┬─────┘
             │                 │                 │
             └─────────────────┼─────────────────┘
                               ▼
                       ┌──────────────┐
                       │ Logique métier│
                       └──────┬───────┘
                              │
           ┌──────────────────┼──────────────────┐
           ▼                  ▼                  ▼
      ┌──────────┐       ┌──────────┐       ┌──────────┐
      │ Dossiers │       │ Clients  │       │  Tâches  │
      └────┬─────┘       └──────────┘       └──────────┘
           │
     ┌─────┼───────────┬────────────┐
     ▼     ▼           ▼            ▼
 Documents  Time      Agenda     Facturation
            Tracking                 │
                                     ▼
                                    PDF
```

---

# 🎓 Ce que ce projet démontre

LexPro met en pratique plusieurs compétences importantes en développement web :

* 🏗️ Architecture **MVC**
* 🐘 Développement backend avec **PHP**
* ⚡ Utilisation du framework **Symfony**
* 🗄️ Gestion d'une base de données relationnelle
* 🔗 Utilisation de **Doctrine ORM**
* 👥 Gestion des utilisateurs et des rôles
* 🔐 Authentification et autorisation
* 🛡️ Contrôle d'accès avec **Security Voters**
* 📄 Gestion documentaire
* ⏱️ Suivi du temps
* 💰 Logique de facturation
* 📑 Génération de documents PDF
* 📅 Intégration d'un calendrier interactif
* 🎨 Construction d'une interface d'administration
* ✅ Validation et sécurité côté serveur

---

# 🚧 Évolutions possibles

Des améliorations pourraient être envisagées dans une future version :

* 📧 Notifications et rappels par e-mail
* 🔔 Système de notifications internes
* 📊 Rapports et statistiques avancés
* 🔎 Recherche avancée dans les dossiers et documents
* 📁 Versioning des documents
* 🔐 Authentification à deux facteurs (2FA)
* ☁️ Stockage documentaire distant
* 📤 Export de données
* 🧪 Extension de la couverture des tests automatisés
* 📱 Amélioration continue de l'expérience mobile

---

# 👨‍💻 Auteur

**Med Khalil Kribi**
GitHub: [@Anis-kribi](https://github.com/Anis-kribi)

> **LexPro — Legal Professional Management Platform**

---

```
