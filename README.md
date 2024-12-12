# Backend - Plateforme de mise en relation des acteurs agricoles

## Description

Ce backend, développé en Laravel, alimente les API nécessaires pour les deux frontends (Admin et User) de la plateforme de mise en relation des acteurs agricoles.

## Prérequis

Avant de démarrer, assurez-vous d'avoir installé :

-   PHP 7.x ou plus récent
-   Composer
-   MySQL ou tout autre SGBD compatible

## Installation et Démarrage

1. **Configurer l'environnement** :

    - Dans le répertoire `Back`, dupliquez le fichier `.env.example` et renommez-le en `.env`.
    - Mettez à jour les informations de connexion à la base de données dans le fichier `.env`.

2. **Installer les dépendances** :

    - Depuis le répertoire `Back`, exécutez la commande suivante dans votre terminal :
        ```bash
        composer install
        ```

3. **Installer la base de données** :

    - Importez le fichier SQL `agriconnect.sql` dans votre serveur MySQL via votre interface MySQL préférée ou en utilisant la ligne de commande.

4. **Exécuter les migrations de la base de données** :

    - Pour créer les tables nécessaires dans la base de données, exécutez la commande suivante :
        ```bash
        php artisan migrate
        ```

5. **Démarrer le serveur de développement** :
    - Lancez le serveur Laravel avec la commande suivante :
        ```bash
        php artisan serve
        ```
    - Vous pouvez accéder à l'application à l'adresse [http://localhost:8000](http://localhost:8000).
