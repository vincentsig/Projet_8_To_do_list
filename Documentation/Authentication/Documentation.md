# Documentation technique concernant l’implémentation de l’authentification

![cover](/Documentation/Authentication/images/to_do_cover.jpg)

## Introduction

Cette documentation se base sur la documentation officielle de Symfony. Elle vous permettra de comprendre comment l’implémentation de l'authentification a été faite dans le projet To-Do&CO, mais aussi de façon plus générale comment fonctionne le composant Security de Symfony. Vous serez alors quel(s) fichier(s) il faut modifier et pourquoi, comment s’opère l’authentification et où sont stockés les utilisateurs.

> **\*Note** : L’anglais étant la langue par convention utilisé en langage de programmation, les notions clé auront leurs traductions entre parenthèse (**traduction**). Vous serrez sûrement amené à retrouver ces termes sur d’autres documentations ou même directement dans du code. Pour ne pas être perdu, il est important d’apprendre ces termes en anglais.\*

## Petit rappel

Tout d’abord, il est important de comprendre deux notions lorsque l’on parle de sécurité, l’authentification (**authentication**) et l’autorisation (**authorisation**).

**Authentication** : Il s’agit de vérifier l’identité d’un utilisateur à travers différentes méthodes
d’identification ( username + password, certificats, API tokens, etc).

**Authorisation** : On va ici vérifier si vous avez les droits nécessaires pour effectuer différentes actions. Dans Symfony, il existe différentes façons de gérer ces authorisations, nous reviendrons plus en détails sur ces notions.

## Un nouveau système d'authentification sur Symfony

Pour l'application ToDo & Co, il a été choisi de mettre en place le nouveau système d’authentification de Symfony arrivé depuis la 5.1.
Lors de la sortie de Symfony 6, il y a de très grandes probabilités que ce nouveau système remplace de façon définitive le Guards système (utilisé depuis la 2.8).

### Décortiquons et essayons de comprendre le nouveau Security Component de Symfony

Et si l’on commençait par cette citation : _"Une image vaut mille mots"_.

![shema](/Documentation/Authentication/images/shema.png)

Parce que le system d’authentification de Symfony est assez complexe, il est bien d’avoir une vue d’ensemble avant de passer aux explications.
Lorsqu’un utilisateur va vouloir se connecter, il va y avoir principalement 5 étapes :

- POST /login Représente la **requête** envoyée par l’utilisateur pour se connecter.
- Le **Firewall** est votre système d'authentification, sa configuration définit comment vos utilisateurs pourront s'authentifier.
- L’**authenticators** est une classe qui vous donne un contrôle complet sur votre processus d'authentification. (Gestion des redirections, exceptions, messages flash ...).
- Le concept de **Passeport** représente l’utilisateur qui souhaite s’authentifier, comme dans la vraie vie.
  Par exemple, lorsqu’un utilisateur va vouloir se connecter via un formulaire un objet **passeport** va être retourné contenant l’utilisateur, les identifiants (**credentials**), et tout autres informations (badges). Pour mieux comprendre les **badges** voici quelques exemples :
  _ CsrfTokenBadge
  _ RemenberMeBadge \* PasswordUpgradeBadge
- Les Events permettent de « suivre » le processus d’authentification. Par exemple, on pourra écouter un évènement et effectuer une action si l’utilisateur a saisis des mauvais credentials.

## Par ou commencer?

1. Installation du Security-Bundle

Il me semble important de rappeler qu’avec Symfony, suivant les projets sur lesquelles vous allez avoir à travailler il n’est pas obligatoire que le composant Security soit présent dans votre application. Il est donc important de vérifier les bundles présent dans votre application en exécutant la commande suivant:

    php bin/console config:dump-reference

Voici la liste de nos bundles dans ToDo & Co, on peut voir que le bundle-security est bien présent:

![bundle-list](/Documentation/Authentication/images/bundle-list.png)

Si le SecurityBundle n’est pas présent il faudra alors l’installer avec Symfony flex, tapez la commande suivante :

    composer require symfony/security-bundle

---

2. Création d'une classe utilisateur

Le plus simple est de commencer par créer notre entité User. Vous pouvez créer manuellement cette entité ainsi que son repository. Mais Symfony possède un utilitaire très pratique le MakerBundle. Je vous conseille d’utiliser cet outil qui va vous guider dans la création de votre entité et générer son repository.

Dans votre terminal exécuté la commande suivante :

        php bin/console make:user

Renseigner les différents champs et valider la création de votre entité. Une fois l’entité créée il est important de comprendre la magie qui s’est opérée derrière cette commande.

- Votre entité user implémente la userInterface qui possède un contrat avec 5 méthodes. Chaque utilisateur doit implémenter cette interface.
- Votre fichier `security.yaml` a été modifié au niveau de la section **provider**, nous y reviendrons plus en détail dans la prochaine partie.

Pour finaliser la création de nos utilisateur, il ne faudra pas oublier d'effectuer et d'exécuter une migration pour la nouvelle entité vers la base de données. En effet ToDo & Co utilise une base de données avec l'ORM Doctrine pour stocker ses utilisateurs.

    php bin/console make:migration
    php bin/console doctrine:migrations:migrate

---

3. Configuration du `security.yaml`

Commençons par regarder d'un **point de vue générale** à quoi correspondent chacune des sections de ce fichier.

- **encoders**: l’encodeur va encoder les mots de passe des utilisateurs. C'est donc grâce à cette section que vous allez pouvoir choisir comment seront encoder vos passwords dans votre application.
- **providers**: Les firewalls s'adressent aux providers pour récupérer les utilisateurs et les identifier.
- **firewalls**: Vous permet de définir un ou plusieurs firewalls que vous souhaitez utiliser pour sécuriser certaines parties de votre application. Il définit aussi comment vos utilisateurs s’authentifieront (login form, api token…).
- **access_control**: Vous permet de définir les différentes ressources que vous souhaitez restreindre à des rôles spécifiques.
- **role_hierarchy**: Vous permet de définir différents rôles pour vos utilisateurs ainsi que leurs hiérarchies.

Maintenant jettons un oeil au ficher `security.yaml` présent dans notre application ToDo & Co.  
 ![security.yaml](/Documentation/Authentication/images/security.png)

- **encoders**  
  ![encoders](/Documentation/Authentication/images/encoders.png)
  On a choisi la propriété auto, au niveau du choix de l’algorithme qui encode notre password. Cela permet, de laisser Symfony choisir le meilleur algorithme. La sécurité étant quelque chose qui évolue, un système d’encodage très sécuritaire utilisé aujourd’hui ne le sera peut-être plus dans les années à venir.

- **providers**
  ![encoders](/Documentation/Authentication/images/providers.png)
  Lorsque nous avions créer notre entité **User** avec le maker-bundle, je vous avais dit que notre fichier `security.yaml` avait été modifié. En effet, ici on peut voir que la configuration c’est faite automatiquement. Nos utilisateurs seront récupérés dans notre base de données via notre entité User avec la propriété **username** qui correspondra à l'identifiant.

- **firewalls**
  ![firewalls](/Documentation/Authentication/images/firewalls.png)

      On a vu que notre autorisation est représentée par notre firewalls, celui-ci se compose en deux parties:

  - **dev** : La partie dev contient un patern avec une expression régulière représentant l’accès à diverses routes. La sécurité est désactivée pour ces routes en environnement de dev grâce au `security : false`. Cela permet d’avoir accès à la « debug bar » et aussi de pouvoir accéder à nos assets, comme nos fichiers statiques (css, images, js …).

  - **main**:

    - `lazy` : true permet de ne pas démarrer de session pour les utilisateurs anonyme (ex : quelqu’un qui essaye de s’identifier). Cela permet d’avoir des gains de performance.
    - `provider` : un firewall a besoin de savoir où trouver ses utilisateurs, cela se fait par le biais de ce paramètre, on indique donc le nom du provider que l’on a défini précédemment.
    - `custom_authenticator` : On indique le chemin de notre authenticator. C’est la class qui va s’occuper de tout le processus d’authentification.
    - `logout` : le firewall peut gérer la déconnexion automatiquement pour vous, lorsque vous activez ce paramètre de configuration. Il vous suffit de préciser le chemin (path) et ensuite la redirection (target).

    Vous pouvez ajouter une méthode logout dans votre securityController et indiquer la route. Dans notre application, nous avons choisi de configurer directement la route dans le fichier `config/routes.yaml` :

    ![routes](/Documentation/Authentication/images/routes.png)

- **enable_authenticator_manager**

Pour activer le nouveau système d’authentification de Symfony, il faut ajouter cette ligne de configuration au niveau de security :

![authenticator_enable](/Documentation/Authentication/images/authenticator_enable.png)

- **access_control**

![acces_control](/Documentation/Authentication/images/access_control.png)
La configuration de l’access_control nous permet de contrôler les autorisations au niveau de certaines URL.

- Tous les utilisateurs ont accès à `/login`.
- Seul les utilisateurs avec le rôle ADMIN peuvent accéder aux URL commençant par `/users`.
- Seul les utilisateurs avec le rôle USER et ADMIN ont accès aux URL commençant par `/tasks`.

---

4. Création de notre customer_authenticator
   ![authenticator](/Documentation/Authentication/images/authenticator.png)

Je ne vais pas expliquer dans les moindres détails cette classe, mais on va voir le processus d’authentification étape par étape pour comprendre de façon générale le fonctionnement de cet authenticator.
Premièrement notre class `LoginFormAuthenticator` extends d’une classe abstraite `AbsctractAuthenticator`. L’`AbstractAuthenticator` implement l’interface `AuthenticatorInterface`. Nous avons donc un contrat de 5 méthodes à respecter dans notre class `LoginFormAuthenticator` :

- **supports**
  ![support](/Documentation/Authentication/images/support.png)

La méthode `supports` va être appelé à chaque requêtes exécutées sur notre application. Cette méthode renvoie un booléen. Si la requête exécutée correspond à la logique métier présente, elle va renvoyer `True` et appeler la méthode `authenticate`. Dans notre cas on veut vérifier que la route appelée est `app_login` et qu’il s’agit d’une requête en POST. Dans notre controller on aura donc une route `app_login` (voir partie 5).
Pour tout autres requêtes supports va retourner `False` et la suite du processus d’authentification ne s’exécutera pas.

- **authenticate**
  ![authenticate](/Documentation/Authentication/images/authenticate.png)
  Comme son nom l’indique authenticate va identifier l’utilisateur. Tout d’abord on vérifie avec son identifiant provenant du formulaire qu’il existe bien dans notre base de données.
  _ Si ce n’est pas le cas alors une exception sera lever et la méthode `onAuthenticationFailure` sera appelé.
  _ Si User existe, on va créer un objet Passport dans lequel on va passer comme argument l’utilisateur récupérer dans la base de données, son plain password (`_pasword`) et son csrf token (`csrf_token`) que l’on récupère dans l’objet request provenant du formulaire de login. (voir partie 5). La méthode `onAuthenticationSuccess` sera alors appelée.

- **onAuthenticationSuccess**
  ![success](/Documentation/Authentication/images/success.png)
  Notre utilisateur à bien été authentifié, on peut alors envoyer un message flash et le rediriger, dans notre cas vers la page `homepage`.

- **onAuthenticationFailure**
  ![failure](/Documentation/Authentication/images/failure.png)
  L’authentification a échouer, on récupère l’exception levée dans `authenticate` et on peut ensuite rediriger l’utilisateur vers la page que l’on souhaite. Dans notre application notre utilisateur sera redirigé vers `app_login` pour qu’il puisse essayer de s’identifier de nouveau.

---

5. Création du SecurityController et de la vue.

- **Le Controller**
  ![controller](/Documentation/Authentication/images/controller.png)
  Ici on a notre `loginAction` qui va être appelé lorsque l’utilisateur effectuera une requête vers la route /login. On passe ensuite à la vue (`security/login.html.twig`) le dernier username saisie ainsi que le dernier message d’erreur qui à été levé dans notre **authenticator**.

- **La vue**
  ![formulaire](/Documentation/Authentication/images/formulaire.png)
  On utilise un formulaire avec comme input le nom d’utilisateur, le password ainsi que le `csrf_token` qui est un champ caché.
  le `form novalidate` permet de désactiver les validations de html 5 et de pouvoir tester nos validations coté serveur.
  > **Note**: Pour plus d'informations concernant les csrf_token et les faille xss voir la rubrique "Pour aller plus loin" dans la dernière partie de la documentation.

## D'autres possibilités de gérer les autorisations

Nous avons vu dans les différentes étapes de mise en place du nouveau système de sécurité de Symfony plusieur moyen de gérer les autorisations. Nous avons principalement utilisé les `firewalls` et l'`access_control`. Cependant il existe d'autres possibilités de gérer les autorisations dans une application.

1.  Les annotations  
    On peut utiliser des annotations dans nos controllers pour gérer les autorisations.  
     \* `@IsGranted`: Cette annotation est le moyen le plus simple de restreindre l'accès. Utilisez-le pour restreindre par rôles l'accès en fonction des variables transmises au contrôleur. Voici un exemple d'utilisation :

                /**
                * @Route("/posts/{id}")
                *
                * @IsGranted("ROLE_ADMIN")
                * @IsGranted("POST_SHOW", subject="post")
                */
                public function show(Post $post)
                {
                }
        * `@Security`: Similaire à `@IsGranted`, cette annotation offre plus d'option et de flexibilité

                /**
                * @Security("is_granted('ROLE_ADMIN') and is_granted('POST_SHOW', post)")
                */
                public function show(Post $post)
                {
                    // ...
                }

2.  Les Voters

Les voters sont un moyens très simple et efficace de controller les autorisations sur nos utilisateurs de façons très précises. Un des autres avantages c'est qu'ils sont faciles à tester.

Nous avons un cas concret d'utilisation au sein de notre application ToDo & Co:

![voters](/Documentation/Authentication/images/voters.png)

Ce voter nous permet de controler deux choses :

- Seuls les personnes ayant le `ROLE_ADMIN` peuvent supprimer les `task` qui n'ont pas d'`author`.
- Seul l'`author` peut supprimer sa `task`.

Dernière étape, il faut indiquer dans notre controller sur quelles méthodes on veut effectuer ce controle grâce à `$this->denyAccessUnlessGranted($attributes, $subject)`.

![voters](/Documentation/Authentication/images/voters_controller.png)

## Pour conclure

### **Les fichiers concernés par l'authentification:**

| Fichier                                   | Description                                                                      |
| ----------------------------------------- | -------------------------------------------------------------------------------- |
| `config\packages\security.yaml`           | Notre fichier de configuration du security component                             |
| `src\Security\LoginFormAuthenticator.php` | Notre authenticator qui s'occupe de la gestion de l'authentification             |
| `src\Entity\User.php`                     | Notre entité User implémentant la UserInterface                                  |
| `src\Repository\UserRepository.php`       | Le repository de l'entité User                                                   |
| `src\Controller\SecurityController.php`   | Notre Controller renvoi la vue en lui passant les messages flashs et les erreurs |
| `templates\security\login.html.twig`      | Notre vue qui affiche le formulaire d'authentification                           |
| `src\Security\Voter\TaskVoter.php`        | Notre TaskVoter qui controle les autorisations sur la suppression des taches     |

### **Pour aller plus loin:**

https://symfony.com/doc/current/security.html  
https://symfony.com/doc/current/security/experimental_authenticators.html  
https://symfony.com/doc/current/security/csrf.html
