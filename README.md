
**Initialisation de l’Environnement de Développement**
Un environnement de développement standardisé a été mis en place à l’aide de Docker(environnement conteneurisé), garantissant une configuration homogène pour l’ensemble de l’équipe.

-** Composants de l’environnement :**
Symfony ( PHP)

PostgreSQL (base de données version 15)

Nginx (serveur web)

Composer (gestionnaire de dépendances)


**- Objectifs Pédagogiques**
Implémentation de l’architecture MVC avec Symfony

Gestion des relations entre entités via Doctrine ORM

Mise en place d’un système de rôles sécurisé (ROLE_ADMIN / ROLE_EMPLOYE)

Affichage dynamique des interfaces selon le rôle utilisateur

Collaboration efficace via Git / GitHub (branches, pull requests, merge)

-** Technologies Utilisées**
Symfony 7.2.5

Doctrine ORM

Twig (moteur de templates) et Bootstrap

Git / GitHub (gestion de version et collaboration)

Dompdf pour la génération PDF 

FullCalendar.js pour le calendrier 


**Fonctionnalités Développées**
Gestion des Clients et Employés : L'admin peut ajouter, modifier ou supprimer des clients et des employés, avec des informations telles que le nom, l’adresse, le téléphone, l’email et la zone d’intervention.

Gestion des Services : Création de services avec des tarifs horaires pour les prestations comme le ménage, le nettoyage des vitres, etc.

Planification des Prestations : L'admin peut planifier des prestations, en attribuant des clients, des employés, et des services avec une date et une heure de début. Le prix total est calculé automatiquement en fonction de la durée et du tarif horaire.

Affichage des Prestations pour les Employés : Les employés ont accès à une interface qui leur permet de voir uniquement leurs prestations à venir et passées.

Sécurisation et Rôles : Le système implémente une sécurité basée sur des rôles pour gérer l'accès aux différentes fonctionnalités du site (admin et employé).

**Relations Entre Entités**
Les entités suivantes ont été créées et liées :

User : Liaison avec le rôle (admin ou employé).

Client, Employe, Service, Prestation : Ces entités sont reliées entre elles pour permettre la gestion des missions de nettoyage.

Prestation contient des informations détaillées sur la prestation, y compris la date, l'heure, la durée, le prix total, et les liens vers le client, l’employé et le service.

Les bonus fait :
Génération de PDF : L'admin peut générer une fiche PDF pour chaque prestation, incluant les informations sur le client, le service, l’employé et le prix total.

Filtrage des Prestations : L’admin peut filtrer les prestations par client, service, employé, statut, ou mois.

Vue Calendrier : Intégration d’une vue calendrier pour visualiser la répartition des missions sur le mois, la semaine ou le jour.



