# ⚖️ LexPro — Plateforme de Gestion pour Cabinet d'Avocats

> **LexPro (Legal Professional)** est une application web de gestion conçue pour centraliser et simplifier les opérations quotidiennes d'un cabinet d'avocats.

L'application regroupe dans une même plateforme la **gestion des dossiers juridiques, des clients, des tâches, du temps de travail, des rendez-vous, des documents et de la facturation**, tout en intégrant un système de contrôle d'accès basé sur les rôles.

L'objectif de LexPro est de réduire la dispersion des informations, améliorer le suivi des dossiers et fournir aux différents utilisateurs un environnement de travail **centralisé, sécurisé et structuré**.

---

## 🎯 Objectifs du projet

LexPro a été conçu autour de plusieurs problématiques courantes dans la gestion d'un cabinet :

- 📁 Centraliser les informations liées aux dossiers
- 👥 Organiser les données des clients et des parties prenantes
- ⏱️ Suivre précisément le temps consacré aux activités juridiques
- 💰 Faciliter la facturation et le suivi des revenus
- 📅 Centraliser les rendez-vous et échéances
- 📄 Organiser les documents associés aux dossiers
- ✅ Suivre les tâches, priorités et délais
- 🔐 Contrôler l'accès aux fonctionnalités selon les responsabilités

---

# 🚀 Fonctionnalités

## 📊 Tableau de bord

Une interface centrale permettant d'obtenir rapidement une vue globale de l'activité du cabinet.

Le dashboard présente notamment :

- 📁 Nombre de dossiers actifs
- 💰 Chiffre d'affaires du mois
- ⚠️ Tâches urgentes
- 📈 Indicateurs clés de performance (KPIs)
- 📅 Informations importantes liées à l'activité

L'objectif est de permettre aux utilisateurs d'identifier rapidement les informations nécessitant leur attention.

---

## ⚖️ Gestion des dossiers juridiques

LexPro centralise les informations nécessaires au suivi d'une affaire dans un espace unique.

Chaque dossier peut regrouper :

- 👥 Parties prenantes
- 📋 Informations générales de l'affaire
- 🕒 Historique des activités
- ✅ Tâches associées
- 📄 Documents
- ⏱️ Temps enregistré
- 💰 Informations liées à la facturation

Cette organisation permet d'éviter la dispersion des données entre plusieurs outils.

---

## ⏱️ Time Tracking

Le module de suivi du temps permet d'enregistrer précisément les activités réalisées dans le cadre d'un dossier.

Exemples d'activités :

- ✍️ Rédaction
- ⚖️ Audiences
- 🔎 Recherches
- 📞 Autres activités professionnelles

Le temps enregistré peut ensuite être utilisé comme base pour la facturation.

---

## 💰 Facturation

LexPro intègre un système de facturation permettant de générer des documents professionnels à partir des prestations enregistrées.

Le système prend en charge :

- Calcul du montant **HT**
- Calcul de la **TVA**
- Calcul du montant **TTC**
- Génération des factures
- Export au format **PDF**
- Téléchargement des documents générés

La génération des PDF est réalisée avec **Dompdf**.

---

## 📅 Agenda & Calendrier

Un calendrier interactif permet de centraliser les différents rendez-vous et échéances du cabinet.

Le module propose plusieurs vues :

- 📆 Mensuelle
- 📅 Hebdomadaire
- 🗓️ Journalière

L'interface utilise **FullCalendar** pour offrir une navigation et une visualisation claire des événements.

---

## 📄 Gestion documentaire

Les documents associés aux dossiers peuvent être téléversés et organisés directement depuis l'application.

Le système prend notamment en charge :

- 📥 Upload de documents
- 📁 Organisation des fichiers
- 🔗 Association des documents aux dossiers
- 📄 PDF
- 📝 Word
- 📊 Excel

La gestion documentaire permet de conserver les pièces importantes au même endroit que les informations du dossier concerné.

---

## 👥 Gestion des clients

LexPro permet de centraliser les informations relatives aux clients du cabinet.

Le système prend en charge notamment :

- 👤 Clients particuliers
- 🏢 Clients entreprises
- 📋 Informations détaillées
- 📁 Historique associé aux dossiers

---

## ✅ Gestion des tâches

Chaque utilisateur peut suivre les actions à effectuer à travers un système de tâches structuré.

Les tâches permettent notamment de gérer :

- 📝 Description de l'action
- 📅 Échéance
- 🚦 Priorité
- 📊 État d'avancement
- 🔗 Association à un dossier

Des indicateurs visuels permettent d'identifier rapidement les tâches prioritaires ou urgentes.

---

# 👥 Gestion des rôles & permissions

LexPro utilise un système de **Role-Based Access Control (RBAC)** afin d'adapter les permissions aux responsabilités de chaque utilisateur.

Le contrôle d'accès est implémenté avec les **Security Voters de Symfony**.

### 🛡️ Administrateur système

L'administrateur bénéficie d'un accès global permettant notamment de :

- Gérer les comptes utilisateurs
- Configurer l'application
- Superviser l'activité
- Administrer les différentes ressources

### ⚖️ Avocat / Associé

L'avocat peut gérer le cycle de vie de ses dossiers, notamment :

- Gestion des affaires
- Planification des audiences
- Suivi du temps
- Gestion des clients
- Gestion documentaire
- Facturation

### 👩‍💼 Assistant(e) juridique

L'assistant(e) intervient sur les opérations administratives autorisées par son niveau de permission :

- Gestion des rendez-vous
- Classement des documents
- Saisie et gestion des clients
- Support administratif des dossiers

Les permissions sont appliquées côté serveur afin d'empêcher l'accès aux ressources non autorisées.

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
          │ Doctrine ORM   │
          └───────┬────────┘
                  │
                  ▼
          ┌────────────────┐
          │ MySQL / MariaDB│
          └────────────────┘
