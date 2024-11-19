# Work Together - app web

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
