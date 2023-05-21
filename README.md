# Projet de Développement Web - Rapport

## Équipe

- Jordan Baumard
- Charles Hurst
- Pierre Leocadie

**Groupe : 209**

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled.png)

Lien du jeu hébergé : [https://playcountryguesser.netlify.app/](https://github.com/pierreleocadie/CountryGuesserWebSocketServer)

## Sommaire

1. Explication de notre projet
2. Cahier des charges
3. Technologies utilisées 
4. Partie Back-end
    1. La base de données
    2. L’API de Country Guesser
    3. Le serveur websocket
    4. Faire fonctionner l’API de Country Guesser et le serveur websocket en local
    5. Déploiement sur un serveur dans le cloud avec le service cloud Linode
5. Partie Front-End
6. Installer la partie front-end en local
7. Les points d’améliorations à prévoir 

## En quoi consiste notre projet ?

Notre projet est un jeu que nous avons décidé d’appeler **Country Guesser** et directement inspiré d’un autre jeu déjà existant se dénommant **GeoGuessr.**

Country Guesser se joue de la manière suivante :

On donne au joueur le drapeau d’un pays sélectionné de manière aléatoire par le jeu et le joueur doit retrouver le pays sur la carte du monde le plus rapidement possible.

Si le joueur ne parvient pas à trouver, il a droit à 3 indices qui lui indiqueront sur quelle partie de du globe terrestre se trouve le pays.

Country Guesser se joue seul ou à plusieurs. Le principe reste le même pour une partie multi-joueur, le but est de trouver le pays correspondant au drapeau donné le plus rapidement possible avant le joueur adverse. 

Une partie multi-joueur se compose de plusieurs manches. Pour remporter une manche, le joueur doit retrouver en premier le pays correspondant au drapeau choisit aléatoirement.

Le joueur ayant remporté le plus de manches gagne la partie.

## Cahier des charges

Le joueur doit pouvoir :

- S’inscrire
    - Pseudo
    - Email
    - Mot de passe
- Se connecter
    - Pseudo ou email
    - Mot de passe
- Se déconnecter
- Consulter les statistiques de ses parties multi-joueurs
    - Nombre de parties jouées
    - Nombre de parties gagnées
- Consulter le classement des meilleurs joueurs du jeu
- Jouer une partie en solo
    - Pour jouer une partie en solo, le joueur doit avoir un compte et être connecté
- Jouer une partie en multi-joueurs
    - Pour jouer une partie en multi-joueurs, le joueur doit avoir un compte et être connecté
- Créer une partie multi-joueur personnalisée

## Technologies utilisées

- Git pour le versioning du projet
- GitHub pour collaborer sur le code en équipe
- Discord pour faire des points sur l’avancée du projet
- Notion pour s’organiser et organiser le projet

### Back-end

- PHP
    - Le gestionnaire de dépendance Composer
    - Package PHP Workerman (pour le multi-joueur → Serveur Websocket)
- Mysql pour la base de données
- PHPMyAdmin pour gérer la base de données
- Le service cloud Linode pour l’hébergement
- Docker pour déployer sur le serveur dans le cloud :
    - Mysql,
    - PHPMyAdmin,
    - L’API de Country Guesser
    - Le serveur Websocket de Country Guesser pour le multi-joueur
    - Portainer.io pour la gestion et le monitoring des containers Docker
    - Reverse proxy Ngnix pour la redirection de chaque sous-domaine vers le service correspondant

### Front-end

- ReactJS
- Material UI
- Mapbox pour l’affichage du globe terrestre

## Back-end

### La base de données

Voici comment nous avons décidé de structurer notre base de données.

![CountryGuesserDatabase.drawio.png](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/CountryGuesserDatabase.drawio.png)

Lorsqu’un joueur s’inscrit, nous stockons les données du joueur dans **la table `players`** . Le pseudonyme (**le champs `nickname`**) du joueur doit être unique ainsi que son `**email**` et peuvent être d’une **longueur maximale de 255 caractères.** Le mot de passe `password` de l’utilisateur est haché avec **la fonction de hachage** **`bcrypt`** en PHP qui offre l’avantage d’avoir plusieurs hash possible pour un même mot et donc rend les attaques plus difficiles.

Par exemple :
Le mot “testtest” **plusieurs hash possibles avec `bcrypt`**:
- $2y$12$Isc9/0Kjgp183HJHDhFQXO1kAzMpKFqUeIR5GC7emLCMIRUadWNW6
- $2y$12$mGIrZuj36t7mL2EtLfuAIOFWtAf8k/MiKW4nEna/SDfgta.j0P2ie
- $2y$12$nRipYb6VU5WNvIRIj4jwuO6qJq.7vYejyxYFwyT/HjsNMTqDp3nCq
- ….

Le mot “testtest” **un unique hash possible avec `SHA-256` :**
- 37268335dd6931045bdcdf92623ff819a64244b53d0e746d438797349d4da578

Lors de l’inscription nous enregistrons aussi le moment où l’inscription a été faite (date et heure) (**le champs `created_at`**). Une “credential key” (**le champs `credential`**) est également générer pour le joueur, le `**credential**` consiste dans le **hash de la concaténation** suivante avec la **fonction de hachage** **`bcrypt`** : 
**`player_id`** + `**nickname**` + `**email**` + **`password`** + **`created_at`**

Cela permet d’éviter qu’un joueur usurpe l’identité d’un autre joueur, en changeant tout simplement son `**player_id**` et/ou son `**nickname**` et/ou son `**email**` par des informations qui ne sont pas les siennes. Le ********************`credential`** vient en complément de l’identifiant par défaut  du joueur qui est le **`player_id`** qui correspond également à la clé primaire de la table.

Ainsi, lors de la connexion, seules les informations suivantes sont renvoyées au joueur :

- `**player_id**`
- **`nickname`**
- **`email`**
- **`credential`**

Lorsqu’une partie est créée, nous enregistrons les joueurs qui participent à la partie dans **la table `playersGamesParticipants`** avec l’identifiant du joueur (**`player_id`**) et l’identifiant de la partie (**`game_id`**) à laquelle il a participé.

Une partie multi-joueur se compose de plusieurs manches. Lors d’une nouvelle manche, nous la créons dans **la table `playersGamesRounds` .** Nous stockons l’identifiant de la partie (**`game_id`),** l’identifiant de la manche (**`round_id`**) ainsi que la bonne réponse de la manche qui correspond au code du pays à deviner (**`response`**). Nous enregistrons également le moment où la manche a été créée (**`created_at`**).

Lorsque le joueur envoie sa réponse, nous la gardons dans **la table `playersGamesRoundsData`** avec l’identifiant de la partie (`**game_id**`) dans laquelle le joueur se trouve, l’identifiant de la manche (**`round_id`**) pour laquelle il a envoyé sa réponse, son identifiant (**`player_id`**), sa réponse (**`player_response`**) ainsi que le moment où il a envoyé sa réponse (**`created_at`**).

**La table `playersLeaderboard`** contient les statistiques de chaque joueurs en partie multi-joueur, l’identifiant du joueur (**`player_id`**), son nombre de parties jouées (**`games_played`**) et son nombre de partie gagnées (**`games_won`**). Cette table est mise à jour à la fin de chaque partie multi-joueur.

### L’API de Country Guesser

Puisque nous avons décidé de faire la partie front-end de Country Guesser avec React et de faire un jeu multi-joueur, afin de simplifier les interactions entre la base de données et le front-end ainsi que les interactions entre le serveur websocket et la base de données. Nous avons décidé de faire une API (cf. [Documentation - Country Guesser API](https://www.notion.so/Documentation-Country-Guesser-API-51457d0229eb40198094ad8bbfde51fc)). 

Ainsi, l’API évite à la partie front-end (client) et le serveur websocket d’intéragir directement avec la base de données. De plus, l’API nous donne plus de liberté du côté client si nous souhaitons par exemple implémenter un autre type d’interface pour Country Guesser.

Nous nous sommes appuyés sur le design pattern MVC (la seule exception ici, c’est que nous n’avons pas de réelles vues puisqu’il s’agit d’une API) avec **de la POO** afin de construire l’API et la rendre le plus maintenable possible.

![Structure de l’API de Country Guesser avec le design pattern MVC
Le dossier `Lib` est l’équivalent d’un dossier `Utils`](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-05_a_17.05.21.png)

Structure de l’API de Country Guesser avec le design pattern MVC
Le dossier `Lib` est l’équivalent d’un dossier `Utils`

Lors d’une requête à l’API, la page `index.php` qui est utilisé comme routeur, va se charger d’aller chercher le contrôleur correspondant à la requête faite et le contrôleur va aller chercher le modèle qui lui est associé. La réponse généré par le modèle est remonté à l’utilisateur sous forme de JSON. Dans le cas où la requête ne correspond à aucune des routes qui ont été définies ou que la requête ne spécifie aucune route, une redirection est faite vers la documentation de l’API [Documentation - Country Guesser API](https://www.notion.so/Documentation-Country-Guesser-API-51457d0229eb40198094ad8bbfde51fc) 

Par exemple : 

On fait la requête suivante à l’API afin d’obtenir le classement des joueurs :

Requête : [`https://api.countryguesser.deletesystem32.fr/player/getleaderboard`](https://api.countryguesser.deletesystem32.fr/player/getleaderboard)

Requête → Route : `/player/getleaderboard` → `index.php` → Contrôleur : `Player/GetLeaderboard.php` → Modèle : `Player/Leaderboard.php`

![API.drawio.png](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/API.drawio.png)

![Le fichier `index.php` de l’API qui est utilisé comme routeur - Liste des routes qui sont définies](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-05_a_17.08.50.png)

Le fichier `index.php` de l’API qui est utilisé comme routeur - Liste des routes qui sont définies

![Notre simple classe Router dans le dossier `src/Models/Router.php`](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-05_a_17.49.00.png)

Notre simple classe Router dans le dossier `src/Models/Router.php`

Autre exemple :

Si on ne spécifie pas de route ou que la route demandée n’existe pas.

Requête : [`https://api.countryguesser.deletesystem32.fr`](https://api.countryguesser.deletesystem32.fr/login) ou [`https://api.countryguesser.deletesystem32.fr/sqdsqs`](https://api.countryguesser.deletesystem32.fr/login)

Redirection vers : [Documentation - Country Guesser API](https://www.notion.so/Documentation-Country-Guesser-API-51457d0229eb40198094ad8bbfde51fc) 

Voici l’ensemble des requêtes qui sont prises en charge par l’API de Country Guesser :

[`https://api.countryguesser.deletesystem32.fr/login`](https://api.countryguesser.deletesystem32.fr/login)

[`https://api.countryguesser.deletesystem32.fr/register`](https://api.countryguesser.deletesystem32.fr/register)

[`https://api.countryguesser.deletesystem32.fr/game/create`](https://api.countryguesser.deletesystem32.fr/game/create)

[`https://api.countryguesser.deletesystem32.fr/game/update`](https://api.countryguesser.deletesystem32.fr/game/update)

[`https://api.countryguesser.deletesystem32.fr/game/delete`](https://api.countryguesser.deletesystem32.fr/game/delete)

[`https://api.countryguesser.deletesystem32.fr/game/getgamedata`](https://api.countryguesser.deletesystem32.fr/game/getgamedata)

[`https://api.countryguesser.deletesystem32.fr/game/participants`](https://api.countryguesser.deletesystem32.fr/game/participants)

[`https://api.countryguesser.deletesystem32.fr/game/round/create`](https://api.countryguesser.deletesystem32.fr/game/round/create)

[`https://api.countryguesser.deletesystem32.fr/game/round/playeranswer`](https://api.countryguesser.deletesystem32.fr/game/round/playeranswer)

[`https://api.countryguesser.deletesystem32.fr/game/round/check`](https://api.countryguesser.deletesystem32.fr/game/round/check)

[`https://api.countryguesser.deletesystem32.fr/player/playerdata`](https://api.countryguesser.deletesystem32.fr/player/playerdata)

[`https://api.countryguesser.deletesystem32.fr/player/getleaderboard`](https://api.countryguesser.deletesystem32.fr/player/getleaderboard)

[`https://api.countryguesser.deletesystem32.fr/player/updateleaderboard`](https://api.countryguesser.deletesystem32.fr/player/updateleaderboard)

[`https://api.countryguesser.deletesystem32.fr/player/getleaderboardstats`](https://api.countryguesser.deletesystem32.fr/player/getleaderboardstats)

Chaque requête est détaillée dans la [Documentation - Country Guesser API](https://www.notion.so/Documentation-Country-Guesser-API-51457d0229eb40198094ad8bbfde51fc) 

### Le serveur websocket

Afin de rendre le jeu multi-joueur nous avions eu besoin de créer un serveur websocket.
Pour créer le serveur websocket, nous avons utiliser le package PHP Workerman.

Lorsqu’un joueur lance une partie multi-joueur, le client se connecte au serveur websocket et place le joueur en file d’attente si aucun autre joueur n’est en recherche de partie. Si un autre joueur en recherche de partie est trouvé, une partie (une `room`) se crée.

À chaque fois qu’un client se connecte, il envoie un pays aléatoire à deviner pour la première manche de la partie, au serveur. C’est donc le pays aléatoire envoyé par le dernier joueur ayant rejoint une partie qui sera le pays à deviner lors de la première manche.

Lorsqu’une manche se termine, les clients de la partie sont informés par le serveur que la manche est terminée et lorsqu’une nouvelle manche est créée les clients de la partie sont également informés par le serveur.

Lorsqu’une manche se termine, chaque client de la partie envoie un pays aléatoire à deviner pour la prochaine manche au serveur.

Si un joueur quitte une partie, les autres joueurs sont exclus de la partie, la partie est supprimée sur le serveur websocket et la partie est également supprimée de la base de données ainsi que toutes les données liées à cette partie.

Lorsque la partie est terminée, les clients sont informés par le serveur et le serveur envoie aux clients toutes les données de la partie (les réponses de chaque joueur pour chaque manche, le vainqueur de chaque manche…)

Le serveur websocket permet d’envoyer en même temps à chaque client les bonnes informations à chaque étape, pour le bon déroulement d’une partie. Le serveur websocket agit un peu comme un routeur de l’information entre les joueurs et garantie l’état de la partie.

Nous avons définis pour les interactions client ←→ serveur websocket, les types de messages, les messages, le formats des messages qui peuvent être envoyés du client → serveur websocket et du serveur websocket → client. (Pour plus de détails voir [Documentation - Country Guesser API](https://www.notion.so/Documentation-Country-Guesser-API-51457d0229eb40198094ad8bbfde51fc) dans la section **********************************Discussion client ←→ Websocket server********************************** )

Exemple de communication serveur websocket → client

![Capture d’écran 2023-01-06 à 14.36.07.png](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-06_a_14.36.07.png)

Pour les interactions serveur websocket → API Country Guesser, nous faisons les appels API avec cURL (fichier **`server/API.php`**). Exemple ci-dessous :

![Capture d’écran 2023-01-06 à 18.39.23.png](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-06_a_18.39.23.png)

Les interactions :
client ↔ serveur websocket,
serveur websocket ↔ API Country Guesser
sont détaillés dans la [Documentation - Country Guesser API](https://www.notion.so/Documentation-Country-Guesser-API-51457d0229eb40198094ad8bbfde51fc) dans les sections **********************************Discussion client ←→ Websocket server*** et **************************Websocket server → API**************************

### Faire fonctionner l’API de Country Guesser et le serveur websocket en local

Afin de faire fonctionner l’API de Country Guesser en local, il est important de modifier le fichier **`DatabaseConnection.php`** qui se trouve dans le dossier **`CountryGuesserAPI/src/Models/Database`** afin de mettre les bons identifiants de connexion à la base de données. Puis ouvrez votre terminal et rendez-vous dans le dossier **`CountryGuesserAPI/src/`**, lancez la commande **`php -S localhost:8000`**

![Capture d’écran 2023-01-06 à 14.11.58.png](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-06_a_14.11.58.png)

Pour faire fonctionner le serveur websocket avec l’API CountryGuesser en local, il faut modifier le fichier `**API.php**` qui se trouve dans le dossier **`CountryGuesserWebSocketServer/server/`** afin de modifier la varibable **`$rootUrl`** par celui de l’API hébergée en local : **`http://localhost:8000`** . Puis ouvrez votre terminal et rendez-vous dans le dossier **`CountryGuesserWebSocketServer`**, lancez la commande **`php server/server.php start`**

![Capture d’écran 2023-01-06 à 14.15.31.png](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-06_a_14.15.31.png)

### Déploiement sur un serveur dans le cloud avec le service cloud Linode

Dans le but de pouvoir travailler plus facilement ensemble, nous avons déployé les services suivants  : 
- notre base de données Mysql,

- deletesystem32.fr:2818

- PHPMyAdmin pour gérer plus simplement la base de données,

- [https://phpmyadmin.countryguesser.deletesystem32.fr/](https://phpmyadmin.countryguesser.deletesystem32.fr/)

- l’API de Country Guesser

- [https://api.countryguesser.deletesystem32.fr/](https://api.countryguesser.deletesystem32.fr/)

- le serveur websocket de Country Guesser

- [ws://ws.countryguesser.deletesystem32.fr/](https://ws.countryguesser.deletesystem32.fr/)
- [https://ws.countryguesser.deletesystem32.fr/](https://ws.countryguesser.deletesystem32.fr/)

- un reverse proxy ngnix pour faire des redirections

- [https://proxymanager.deletesystem32.fr/](https://proxymanager.deletesystem32.fr/)

- Portainer pour la gestion et le monitoring des containers Docker

- [https://portainer.deletesystem32.fr/](https://portainer.deletesystem32.fr/)

Nous avons utilisé Docker afin de pouvoir déployer, gérer et monitorer plus simplement chaque service.

Puisque nous avons qu’un seul serveur, nous avons utilisé un reverse proxy ngnix associé à une base de données MariaDB afin de pouvoir faire des redirections vers les ports de chaque service qui sont lancés dans des containers Docker en utilisant des sous-domaines. De plus le reverse proxy ngnix nous permet de bénéficier de certificats SSL pour nos différents services et donc d’avoir de l’HTTPS.

![Fonctionnement d’un reverse proxy](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%201.png)

Fonctionnement d’un reverse proxy

![Gestion du nom de domaine utilisé pour le projet et des sous-domaines associés avec le service cloud Linode](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-06_a_17.41.40.png)

Gestion du nom de domaine utilisé pour le projet et des sous-domaines associés avec le service cloud Linode

![Les redirections depuis les sous-domaines vers des ports spécifiques liés aux différents services que nous avons déployés ](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-05_a_16.58.14.png)

Les redirections depuis les sous-domaines vers des ports spécifiques liés aux différents services que nous avons déployés 

![Paramétrage d’une redirection](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-05_a_17.00.17.png)

Paramétrage d’une redirection

![Paramétrage d’une redirection - Certificat SSL](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-05_a_17.00.22.png)

Paramétrage d’une redirection - Certificat SSL

Ce qui nous permet par exemple d’accéder à PHPMyAdmin avec le lien suivant :
[https://phpmyadmin.countryguesser.deletesystem32.fr/](https://phpmyadmin.countryguesser.deletesystem32.fr/) au lieu de [http://deletesystem32.fr:2817](http://deletesystem32.fr:2817/)
ou encore de se connecter au serveur websocket avec le lien suivant :
[ws://ws.countryguesser.deletesystem32.fr/](https://ws.countryguesser.deletesystem32.fr/)
Cela a pour but de nous simplifier la tâche pour accéder à nos différents services mais également afin que ce soit plus lisible dans le code, sans que nous ayons à nous demander sur quel port avons nous héberger tel ou tel service.

Nous avons également créé un script python pour synchroniser les différents repo GitHub et la production sur le serveur dans le cloud Linode. On crée un cronjob sur serveur cloud qui execute le script python tous les x temps. Le script python va aller vérifier si un nouveau commit a été fait sur le repo souhaité et comparer avec la version existante en production, si les deux versions diffèrent, il va supprimer le dossier du repo existant sur le serveur, arrêter et supprimer le container docker lié, clone le repo, build et run le container docker.

```python
import os, json

PERSONAL_ACCESS_TOKEN = "YOUR_PERSONAL_GITHUB_ACCESS_TOKEN"
OWNER = "GITHUB_REPO_OWNER"
REPO = "GITHUB_REPO_NAME"
CONTAINER = "DOCKER_CONTAINER_NAME"
PATH = "PATH_TO_REPO"
PORT_MAPPING = "PORT_MAPPING_FOR_DOCKER_CONTAINER"

CURL_REQUEST = f'curl --request GET \
    --url "https://api.github.com/repos/{OWNER}/{REPO}/commits" \
    --header "Accept: application/vnd.github+json" \
    --header "Authorization: Bearer {PERSONAL_ACCESS_TOKEN}" > {PATH}/lastCommit-1.json'

BUILD_AND_RUN_CONTAINER = f"cd {PATH}/; \
			git clone https://github.com/{OWNER}/{REPO}.git; \
                        docker stop {CONTAINER}; \
                        docker rm {CONTAINER}; \
                        cd {PATH}/{REPO}; \
                        docker build -t {CONTAINER} .; \
                        docker run -d --name {CONTAINER} -p {PORT_MAPPING} {CONTAINER}"

def checkRemoveBuildRun():
    if(os.path.exists(f"{PATH}/{REPO}")):
        os.system(f"rm -rf {PATH}/{REPO}")
    os.system(BUILD_AND_RUN_CONTAINER)

if(os.path.exists(f"{PATH}/lastCommit.json")):
    os.system(CURL_REQUEST)
    with open(f"{PATH}/lastCommit.json", "r") as f:
        if(f.read() != open(f"{PATH}/lastCommit-1.json", "r").read()):
            checkRemoveBuildRun()
        os.system(f"rm {PATH}/lastCommit.json; \
        mv {PATH}/lastCommit-1.json {PATH}/lastCommit.json")
else:
    os.system(CURL_REQUEST)
    checkRemoveBuildRun()
    os.system(f"mv {PATH}/lastCommit-1.json {PATH}/lastCommit.json")
```

![Cronjob pour synchroniser](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-07_a_12.56.52.png)

Cronjob pour synchroniser

Vous pouvez si vous le souhaitez consulter directement notre base données avec PHPMyAdmin, ou nos différents containers Docker des différents services avec Portainer à l’aide des identifiants que nous vous avons créé pour l’occasion et que nous vous avons transmis par mail.

PHPMyAdmin : [https://phpmyadmin.countryguesser.deletesystem32.fr/](https://phpmyadmin.countryguesser.deletesystem32.fr/)
Portainer : [https://portainer.deletesystem32.fr/](https://portainer.deletesystem32.fr/)

### Dashboard Linode pour gérer et monitorer le serveur cloud

![Dashboard linode qui permet de gérer et monitorer le serveur](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Capture_decran_2023-01-05_a_16.57.03.png)

Dashboard linode qui permet de gérer et monitorer le serveur

### Schéma récapitulatif de l’infrastructure back-end de Country Guesser

![L’infrastructure de notre serveur cloud](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Infrastructure.drawio.png)

L’infrastructure de notre serveur cloud

## Front-end

### Accueil

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%202.png)

Pour la page d’accueil, nous avons opté pour une interface futuriste et simple à utiliser pour tout le monde.

Elle comporte une barre de navigation avec quelques onglets comme les statistiques des parties des joueurs, une page à propos du projet et un onglet pour pouvoir se connecter.

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%203.png)

La page statistiques contient : (uniquement les statistiques du mode multijoueur !)

- le nombre de parties gagnées du joueur
- le nombre de parties perdues
- le nombre de parties jouées
- le classement des meilleurs joueurs

Par la suite, nous souhaiterions ajouter une barre de recherche afin de trouver directement sa position dans le classement.

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%204.png)

La page “À propos” contient la signature du site, c’est-à-dire nos noms et le nom de l’IUT. Une animation de défilement est présente type “Star Wars” qu’on peut mettre en pause en cliquant sur l’écran.

### Fonctionnalités pratiques

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%205.png)

Possibilité de lancer une partie depuis n’importe quelle page grâce au bouton flottant situé en bas à droite.

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%206.png)

Le site est entièrement responsive, ce qui permet à n’importe qui de jouer sur mobile, tablette et PC depuis n’importe où.

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%207.png)

Lors du chargement des informations nécessaires au jeu, une barre de chargement en haut défile et un rond de chargement est situé à la place du drapeau.

De plus, les boutons sont désactivés pour indiquer à l’utilisateur qu’il doit patienter.

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%208.png)

L’interface du jeu propose des indices sous forme de cercles. Le pays à deviner est présent dans le cercle. Il est possible d’utiliser jusqu’à 3 indices :

- Cercle très large (difficile)
- Cercle large (assez difficile)
- Cercle proche (facile)

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%209.png)

Quelques animations sont présentes sur le jeu, les voici :

- Quand on abandonne la partie, la caméra vole jusqu’au pays qui était à deviner
- Quand on valide un mauvais pays, l’écran tremble pour montrer que ce n’est pas la bonne réponse

 

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%2010.png)

Lorsqu’on sélectionne un pays, le bouton “Confirmer ma réponse” se débloque, une popup apparaît à l’endroit cliqué pour indiquer le pays sélectionné. Il est également indiqué en bas à droite de la carte du globe et juste au dessus du bouton “Confirmer”.

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%2011.png)

Lorsqu’un joueur cherche une partie multijoueurs disponible, il y a une animation de terre qui tourne, une citation de motivation et un questionnaire pour l’occuper !

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%2012.png)

![Untitled](Projet%20de%20De%CC%81veloppement%20Web%20-%20Rapport%2023d0e64fd673475b9e4c7fed3f79e040/Untitled%2013.png)

Un joueur peut se connecter/créer son compte. Il y aune page mot de passe oublié mais celle-ci ne fonctionne pas. C’est une amélioration que nous prévoyons de faire par la suite.

## Installer la partie front-end en local

**Étape 1 -**
- Rendez-vous dans le dossier CountryGuesserUI 
ou 
- Ouvrez un terminal et clonez le dépôt Git suivant : [https://github.com/jordanbmrd/CountryGuesser](https://github.com/jordanbmrd/CountryGuesser) à l’aide de la commande :

```bash
git clone https://github.com/jordanbmrd/CountryGuesser.git && cd CountryGuesser
```

**Étape 2 -** Installez les dépendances (il faut avoir installé NodeJS au préalable pour utiliser la commande npm, si vous ne l’avez pas installé, installez-le et relancez votre terminal) :

```bash
npm install
```

**Étape 3 -** Si vous avez installé la partie backend en local, le lien du websocket et le lien de l’api seront différents, modifiez ceux qui sont dans le fichier .env avec les URL que vous souhaitez utilisez, sinon, laissez-ceux à distance.

**Étape 4 -** Lancez le serveur local :

```bash
npm start
```

## Les points d’améliorations à prévoir

- Optimisation de la base de données
- Optimisation des interactions API ←→ base de données, utilisation de procédures et fonctions stockées SQL
- Refactoring et optimisation du serveur websocket
- Améliorer notre architecture MVC
- Donner à l’utilisateur la possibilité de modifier ses informations (pseudonymes, email, mot de passe)
- Intégrer une messagerie lorsque le joueur se trouve dans une partie multijoueurs
- Gérer le cas du mot de passe oublié
