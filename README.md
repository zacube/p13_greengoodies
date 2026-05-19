
# Projet Green Goodies

## Pré-requis
* PHP >= 8.2
* Composer
* Symfony 8
* MariaDB

## Installation

### Composer
Dans un premier temps, installer les dépendances :
```bash
composer install
```


## Configuration

Créer un fichier d'environnement ".env.local" à la racine et le renseigner avec les valeurs suivantes :

```bash
###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://USER:PASSWORD@HOTE:3306/BASE"
###< doctrine/doctrine-bundle ###

###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=passphrase
###< lexik/jwt-authentication-bundle ###
```



### Base de données



#### Créer la base de données
```bash
symfony console doctrine:database:create
```

#### Exécuter les migrations
```bash
symfony console doctrine:migrations:migrate -n
```

#### Charger les fixtures
```bash
symfony console doctrine:fixtures:load
```

### Clés d'authentification JWT
Créer le dossier /config/**jwt** 
```bash
php bin/console lexik:jwt:generate-keypair
```



### Serveur web
```bash
symfony serve
```

### Utilisateurs
4 utilisateurs sont créés :

| login       | mot de passe  |
|-------------|---------------|
| 111@111.com | aze           |
| 222@222.com | aze           |
| 333@333.com | aze           |
| 444@444.com | aze           |


## Accès API
Débloquer l'accès d'un utilisateur en se rendant sur la page "_Mon compte_" et en cliquant sur le bouton "_Activer mon accès API_"

#### Accéder à l'API
 1. via ApiPlatform :  
    http://127.0.0.1:8000/api/docs
 2. via Postman :
    POST http://127.0.01:8000/api/login 
 
