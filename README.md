# Work Together - app web

## Description

Work Together est une application web permettant de louer des unités d'une baie de datacenter.
Vous pouvez consulter les unités disponibles, les réserver, les libérer et les gérer.

## Roadmap

- [x] Etablir un dictionnaire de données & des règles de gestion
- [x] Créer le MCD
- [x] Créer les entités
- [x] Créer la base de données & les tables
- [ ] Mettre en place la stack logicielle avec Docker compose
- [ ] Rédiger les textes de la page d'accueil & à propos
- [ ] Afficher les offres commerciales
- [ ] Mettre en place la vérification de disponibilité des unités
- [ ] Mettre en place les commandes (sans paiement)
- [ ] Gérer les annulations de commandes
- [ ] Gérer la génération de factures par PDF
- [ ] Gérer l'authentification & les rôles
- [ ] Créer l'espace client (avec gestion des données personnelles)
- [ ] Mettre en place la gestion des unités, leur statut, leur prix, etc.
- [ ] Mettre en place l'affichage de l'état des unités en temps réel
- [ ] Ajouter l'historique des interventions
- [ ] Mettre en place les tests unitaires
- [ ] Internationalisation (français, anglais)
- [ ] Section FAQ

![gantt.png](assets/doc/gantt.png)

## Schéma de la base de données

![schema.png](assets/doc/schema.png)

![dbms-schema.png](assets/doc/dbms-schema.png)

## Installation

Copier le fichier `.env.dist` en `.env` et modifier les variables d'environnement (notemment la chaîne de connection à la base de données).

```bash
composer install
```
```bash
php bin/console doctrine:database:create
```
```bash
php bin/console doctrine:migrations:migrate
```
