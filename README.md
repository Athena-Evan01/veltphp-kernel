# Velt Kernel

Kernel de base du framework Velt.

Ce depot contient le sous-module `velt/kernel`, c'est-a-dire la couche commune qui doit servir de socle aux futurs modules HTTP, CLI, UI, Database et Preview.

Le kernel n'est pas encore un framework complet. Il est volontairement incomplet sur certains points, et c'est normal a ce stade. Son role est de fournir:

- un contrat de base pour l'application;
- un container minimal avec une trajectoire claire vers PSR-11;
- une gestion de configuration;
- un loader `.env` minimal;
- un dispatcher d'evenements synchrone;
- un handler centralise des exceptions;
- un cycle de vie de base pour preparer le branchement HTTP et CLI.

## Etat actuel

Le package kernel existe, compile, et les tests passent.

- PHP cible: 8.2+
- Namespace: `Velt\Kernel`
- Autoload PSR-4: actif
- Tests PHPUnit: actifs

Ce qui est deja en place:

- `Application`
- `Container`
- `ConfigRepository`
- `EnvRepository`
- `EventDispatcher`
- `DefaultExceptionHandler`
- `ServiceProvider`
- les contrats publics du kernel

Ce qui reste encore a consolider:

- la cohesion du container avec PSR-11;
- la gestion des dependances scalaires dans l'autowiring;
- la discipline du cycle `register -> boot -> runtime`;
- l'orchestration centrale des erreurs pour HTTP et CLI;
- la documentation de ce qui est stable et de ce qui ne l'est pas encore.

## Comment lire ce README

Ce README sert de document de pilotage.

Il repond a trois questions:

1. Qu'est-ce qui existe deja?
2. Qu'est-ce qui est prioritaire avant d'ajouter HTTP et CLI?
3. Comment identifier une erreur et quoi faire pour la corriger?

## Périmètre

Inclus dans le kernel:

- fondations d'application;
- container de services;
- configuration;
- environnement;
- service providers;
- events synchrones internes;
- gestion centralisee des exceptions;
- base pour l'autowiring prudent.

Exclus du kernel:

- routing HTTP;
- controllers;
- commands CLI;
- responses HTTP;
- render UI;
- acces base de donnees;
- preview mobile;
- logique metier applicative.

## Backlog priorise

### Bloquant

Ces points doivent etre solides avant de brancher HTTP ou CLI.

| Point | Risque | Fichier principal | Remediation attendue |
|---|---|---|---|
| Container PSR-11 | contrat public instable | `packages/kernel/src/Container.php` | stabiliser `get()` / `has()` et formaliser le contrat d'erreur |
| Autowiring prudent | resolution trop optimiste | `packages/kernel/src/Container.php` | gerer mieux les scalaires, valeurs par defaut, aliases et classes non instanciables |
| Lifecycle provider | boot non totalement verrouille | `packages/kernel/src/Application.php` | garantir l'ordre `register` puis `boot`, sans re-entrance problematique |
| Exception handling centralise | erreurs pas encore orchestrees partout | `packages/kernel/src/Exceptions/DefaultExceptionHandler.php` | brancher `report()` et `render()` dans le runtime de niveau superieur |
| Base environment | mode par defaut et chargement `.env` | `packages/kernel/src/Application.php`, `packages/kernel/src/Env/EnvRepository.php` | clarifier les defaults, la conversion et les cas d'erreur |

### Important

Ces points ne bloquent pas tout de suite, mais ils vont vite devenir source de dette technique.

| Point | Risque | Fichier principal | Remediation attendue |
|---|---|---|---|
| Cohesion config | `has()` et `get()` peuvent diverger sur `null` | `packages/kernel/src/Config/ConfigRepository.php` | definir une semantique stable pour les valeurs nulles |
| Event payload | signature simple mais pas encore riche | `packages/kernel/src/EventDispatcher.php` | documenter clairement ce que recoivent les listeners |
| ServiceProvider creation | creation du provider trop directe | `packages/kernel/src/Application.php` | clarifier la creation par classe et les erreurs attendues |
| Documentation publique | README et code peuvent diverger | `README.md`, `packages/kernel/README.md` | garder une source de verite unique pour les contrats |

### Secondaire

Ces points ameliorent la qualite mais ne doivent pas retarder le branchement HTTP/CLI.

| Point | Risque | Remediation attendue |
|---|---|---|
| README plus pedagogique | onboarding plus lent | garder une liste claire des erreurs et des remedes |
| Fixtures plus completes | couverture de cas limites plus faible | ajouter des fakes pour les scenarii de runtime |
| Messages d'erreur homogenes | lecture plus difficile en debug | uniformiser les messages des exceptions internes |

## Issues du kernel

Le travail du kernel est decoupe en issues techniques.

- [Issue 01 - Initialiser le package Kernel](issues/01-initialiser-package-kernel.md)
- [Issue 02 - Creer les contrats fondamentaux](issues/02-creer-contrats-fondamentaux.md)
- [Issue 03 - Implementer le container minimal](issues/03-implementer-container-minimal.md)
- [Issue 04 - Ajouter configuration et bootstrap application](issues/04-ajouter-configuration-bootstrap-application.md)
- [Issue 05 - Ajouter service providers et lifecycle application](issues/05-service-providers-lifecycle-application.md)
- [Issue 06 - Ajouter EventDispatcher synchrone minimal](issues/06-event-dispatcher-synchrone-minimal.md)
- [Issue 07 - Ajouter Env loader et modes application](issues/07-env-loader-modes-application.md)
- [Issue 08 - Ajouter ExceptionHandler centralise](issues/08-exception-handler-centralise.md)
- [Issue 09 - Renforcer container avec autowiring prudent et compatibilite PSR-11](issues/09-container-autowiring-prudent-psr11.md)

## Comment voir les erreurs

Le kernel doit permettre de voir les erreurs a plusieurs niveaux.

### 1. Erreurs de resolution container

Fichiers a surveiller:

- `packages/kernel/src/Container.php`
- `packages/kernel/src/Exceptions/ContainerResolutionException.php`
- `packages/kernel/src/Exceptions/ServiceNotFoundException.php`

Ce que cela veut dire:

- service absent;
- classe non instanciable;
- dependance objet introuvable;
- dependance scalaire non resolvable;
- alias mal configure.

Remediation:

- verifier si le service doit etre `bind`, `singleton`, `instance` ou une simple classe autowiree;
- confirmer que toutes les dependances objet sont elles-memes resolubles;
- si une dependance scalaire est necessaire, elle doit venir d'une configuration explicite;
- si un alias est utilise, verifier la direction exacte de la resolution.

### 2. Erreurs de lifecycle application

Fichiers a surveiller:

- `packages/kernel/src/Application.php`
- `packages/kernel/src/ServiceProvider.php`

Ce que cela veut dire:

- provider invalide;
- provider enregistre apres boot;
- boot appelle deux fois;
- provider qui s'auto-enregistre au mauvais moment;
- ordre `register -> boot` rompu.

Remediation:

- enregistrer tous les providers avant `boot()`;
- ne pas muter la liste des providers pendant un boot en cours;
- garder les providers petits et predecibles;
- utiliser des fakes pour valider l'ordre des appels.

### 3. Erreurs d'environnement

Fichiers a surveiller:

- `packages/kernel/src/Application.php`
- `packages/kernel/src/Env/EnvRepository.php`

Ce que cela veut dire:

- `.env` absent;
- variable inconnue;
- valeur booleenne mal casted;
- mode application inattendu.

Remediation:

- verifier que le `basePath` pointe vers le bon dossier;
- s'assurer que `.env` existe quand il est attendu;
- definir explicitement `APP_ENV` et `APP_DEBUG`;
- ne pas supposer que le mode par defaut convient a tous les contextes.

### 4. Erreurs d'exceptions

Fichiers a surveiller:

- `packages/kernel/src/Exceptions/DefaultExceptionHandler.php`
- `packages/kernel/src/Contracts/ExceptionHandlerInterface.php`

Ce que cela veut dire:

- en debug, on voit trop peu;
- en production, on voit trop;
- le message public n'est pas neutre;
- `report()` n'est pas encore branche au runtime superieur.

Remediation:

- utiliser `render()` pour la structure neutre;
- utiliser `report()` pour le suivi technique;
- garder les details sensibles hors de la sortie publique;
- brancher le handler au futur front controller HTTP et a l'entree CLI.

### 5. Erreurs de config

Fichiers a surveiller:

- `packages/kernel/src/Config/ConfigRepository.php`

Ce que cela veut dire:

- une cle existe mais vaut `null`;
- une cle imbriquee est mal structuree;
- un `get()` retourne le fallback alors que la structure est presente.

Remediation:

- definir clairement la difference entre "absent" et "present mais null";
- tester les chemins en notation par points;
- eviter les structures ambiguës dans la configuration initiale.

## Comment remedier correctement

Quand une erreur apparait, suivre toujours cette sequence:

1. Identifier si le probleme vient du container, de l'application, de l'environnement, des events ou des exceptions.
2. Reproduire avec un test unitaire local.
3. Comparer le comportement avec l'issue correspondante.
4. Corriger la cause racine, pas seulement le message d'erreur.
5. Verifier qu'aucun module HTTP ou CLI n'a ete introduit par accident dans le kernel.

## Pour brancher HTTP et CLI plus tard

Le kernel doit rester le point d'entree commun.

Le futur branchement devra suivre cette logique:

- instancier `Application`;
- charger l'environnement;
- charger la configuration;
- enregistrer les providers;
- boot les providers;
- capturer les `Throwable`;
- appeler `report()`;
- convertir l'erreur via `render()`;
- laisser HTTP ou CLI faire le rendu final.

Ce README doit servir de repere pour verifier que ce workflow reste propre et previsible.

## Verification rapide

Depuis `packages/kernel`:

```bash
vendor/bin/phpunit
```

Le but n'est pas seulement d'avoir des tests verts. Le but est d'avoir un kernel qui:

- reste lisible;
- expose des erreurs exploitables;
- ne depend pas des futurs modules;
- peut recevoir HTTP et CLI sans se contredire.

## Note de lecture

Le package `packages/kernel/README.md` peut garder les details techniques du composant.

Ce README racine sert surtout a:

- resumer l'etat reel du kernel;
- prioriser ce qui bloque la suite;
- documenter comment diagnostiquer et corriger les erreurs avant l'ajout de HTTP et CLI.
