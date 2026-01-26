# JOB-MATCH
# Application Job Dating YouCode

## Présentation

Ce projet est une application web développée dans le cadre d’un **projet académique YouCode**. Il vise à faciliter les événements de **Job Dating** en mettant en relation les apprenants et les entreprises partenaires.

L’application permet la gestion des annonces d’emploi, des entreprises, des apprenants et des candidatures, avec une séparation claire entre **Front Office** et **Back Office**.

---

## Technologies utilisées

* PHP 8.x (framework MVC personnalisé)
* MySQL 8.x (PostgreSQL en bonus)
* PDO (requêtes préparées)
* Twig
* Composer

---

## Fonctionnalités principales

* Authentification sécurisée (Admin / Apprenant)
* Dashboard administrateur avec statistiques
* Gestion des annonces (CRUD + archivage)
* Gestion des entreprises
* Consultation des apprenants
* Front Office apprenant :

  * Consultation des offres
  * Recherche et filtres dynamiques (AJAX)
* Gestion des candidatures (postulation, suivi, statuts)

---

## Sécurité

* Protection CSRF
* Sécurisation contre XSS et SQL Injection
* Validation serveur des données
* Upload CV limité aux fichiers PDF

---

## Environnement de développement

* Serveur : Apache (XAMPP / WAMP / Laragon)
* IDE : Visual Studio Code
* Version VS Code : **1.85+**

### Extensions VS Code recommandées

* PHP Intelephense
* PHP Debug
* Twig Language 2
* SQLTools

---

## Objectif pédagogique

Mettre en pratique les concepts de **MVC**, **sécurité web**, **PDO**, **authentification**, et **architecture PHP** dans un projet concret.
