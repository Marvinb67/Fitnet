

## Comment installer un projet Symfony en local

Assurez-vous que votre machine est équipée de PHP8 et de MySQL ou d'un autre système de gestion de base de données pris en charge par Symfony.

Installez Composer, un gestionnaire de dépendances pour PHP, en suivant les instructions sur le site web de Composer.

Téléchargez ou clonez le code source de votre application Symfony à partir d'un référentiel Git ou d'un téléchargement direct.

Ouvrez une ligne de commande dans le répertoire racine de l'application Symfony et exécutez la commande suivante pour installer les dépendances :

```composer install```
composer install
```

Configurez votre base de données en créant un fichier .env.local à la racine de votre projet. Il doit contenir les informations de connexion à votre base de données locale, comme suit :

```# .env.local
DATABASE_URL=mysql://user:password@127.0.0.1:3306/my_database
```

Créez la base de données en exécutant la commande suivante :

```bloc de code```
php bin/console doctrine:database:create
```

Exécutez les migrations pour créer les tables de la base de données :

```bloc de code```
php bin/console doctrine:migrations:migrate
```

``` `
npm install
```

``` `
npm run build
```

Exécutez l'application en utilisant le serveur Web intégré de Symfony :

```bloc de code```
php bin/console server:start
```

Ouvrez un navigateur web et accédez à l'URL suivante :

http://localhost:8000



## Comment installer un projet Symfony en prod

1. Assurez-vous que votre serveur répond aux exigences de Symfony. Consultez la documentation Symfony pour connaître les exigences système requises.

2. Téléchargez votre code source Symfony sur le serveur de production. Vous pouvez le faire en utilisant Git ou en transférant les fichiers via FTP.

3. Configurez les paramètres de l'application pour la production. Assurez-vous que les paramètres de base de données et de sécurité sont corrects pour la production.

4. Installez les dépendances de l'application en exécutant la commande "composer install --no-dev --optimize-autoloader" dans le répertoire de l'application. Cela installera les dépendances requises pour l'application et optimisera le chargement automatique.

5. Mettez à jour la base de données en exécutant la commande "php bin/console doctrine:migrations:migrate" pour exécuter toutes les migrations de base de données.

6. Configurez le serveur Web pour pointer vers le répertoire web/ de l'application Symfony. Assurez-vous que le serveur Web a les permissions nécessaires pour accéder aux fichiers de l'application.

7. Activez le cache en exécutant la commande "php bin/console cache:clear --env=prod" pour effacer et régénérer le cache de l'application en mode production.

8. Testez l'application pour vous assurer qu'elle fonctionne correctement en production. Vérifiez les journaux d'erreurs pour détecter tout problème.


