# Justifications des choix techniques

> À intégrer dans la partie **Spécifications techniques** (et à rappeler en **Sécurité**).
> Objectif : montrer au jury que l'absence de JWT / MVC / ORM est un **choix assumé et argumenté**,
> pas une lacune.

---

## 1. Sessions PHP plutôt que JWT

**Choix retenu : authentification par session serveur (`$_SESSION`).**

Le site est une application web classique rendue côté serveur (server-side rendering) :
chaque page est générée par PHP puis envoyée au navigateur. Dans ce contexte, la **session
serveur** est la solution la plus adaptée :

- L'état de connexion est stocké côté serveur ; le navigateur ne conserve qu'un identifiant
  de session opaque dans un cookie. Aucune donnée sensible ne circule ni n'est stockée côté client.
- La déconnexion est **immédiate et réelle** : on détruit la session côté serveur. Un JWT, lui,
  reste valide jusqu'à son expiration (on ne peut pas le « révoquer » simplement).
- Sécurité renforcée : régénération de l'identifiant de session à la connexion
  (`session_regenerate_id(true)`) pour prévenir la **fixation de session**.

**Quand le JWT aurait été pertinent :** pour une **API REST sans état (stateless)** consommée par
une application mobile ou un front séparé (React/Vue), où le serveur ne garde aucune session.
Ce n'est pas mon cas : mon front et mon back sont servis par le même serveur PHP.

> **Réponse type au jury** — « Pourquoi pas de JWT ? »
> Parce que mon application est rendue côté serveur, pas une API stateless. La session PHP est
> plus adaptée, permet une déconnexion réelle et évite de stocker des informations côté client.

---

## 2. Architecture procédurale organisée plutôt que MVC

**Choix retenu : PHP procédural avec une séparation claire des responsabilités.**

Je n'utilise pas de framework MVC, mais j'applique le **principe de séparation** qui en est le fondement :

| Rôle (équivalent MVC) | Où c'est dans mon projet |
|---|---|
| **Modèle** (accès aux données) | `fonctions.php` — toutes les fonctions BDD, regroupées par table, recevant `$pdo` en 1er paramètre |
| **Vue** (affichage) | `header.php`, `footer.php`, `navbar.php` + le HTML de chaque page |
| **Contrôleur** (logique de page) | La partie PHP en haut de chaque fichier de page (traitement `$_POST`, redirections) |

Avantages de ce choix pour ce projet :

- **Simplicité et lisibilité** adaptées à la taille du site et à mon niveau de formation :
  pas de couche d'abstraction à maîtriser, le flux d'exécution est direct et facile à expliquer.
- **Aucune dépendance lourde** : le projet tourne sur un simple serveur PHP/Apache (XAMPP),
  sans configuration de routeur ni de conteneur d'injection de dépendances.
- La séparation logique / vue / données reste respectée, donc le code est maintenable.

**Limite reconnue :** sur un projet plus gros ou en équipe, un framework MVC (Symfony, Laravel)
apporterait un **routeur**, une **structure imposée** et de la **réutilisabilité**. C'est la voie
d'évolution naturelle du projet.

> **Réponse type au jury** — « Pourquoi pas de MVC / framework ? »
> J'ai choisi le procédural pour rester simple et maîtriser chaque ligne, tout en respectant la
> séparation modèle/vue/contrôleur. Sur un projet plus ambitieux, je passerais à Symfony ou Laravel.

---

## 3. PDO avec requêtes préparées plutôt qu'un ORM

**Choix retenu : PDO (PHP Data Objects) avec requêtes préparées.**

- **Sécurité** : les requêtes préparées séparent le code SQL des données utilisateur, ce qui
  **neutralise les injections SQL** — la protection la plus importante de l'application.
  J'ai désactivé l'émulation (`PDO::ATTR_EMULATE_PREPARES => false`) pour de vraies requêtes
  préparées côté serveur MySQL.
- **Maîtrise du SQL** : j'écris moi-même mes requêtes (jointures, `GROUP BY`, `AVG`, pagination
  `LIMIT/OFFSET`). Cela me permet de **comprendre et d'optimiser** exactement ce qui est exécuté,
  et de savoir l'expliquer au jury — compétence attendue au DWWM.
- **Aucune surcouche** : un ORM (Doctrine, Eloquent) génère le SQL à ma place, ce qui masque ce
  qui se passe réellement et ajoute une dépendance. Pour un projet de cette taille, PDO suffit.

**Quand l'ORM aurait été pertinent :** sur une grosse application avec de nombreuses entités liées,
un ORM fait gagner du temps (mapping objet↔table, relations, migrations) au prix d'un contrôle
plus fin du SQL.

> **Réponse type au jury** — « Pourquoi pas d'ORM ? »
> J'ai préféré PDO avec requêtes préparées pour garder la maîtrise totale de mon SQL et de la
> sécurité anti-injection. Un ORM comme Doctrine serait le choix logique sur une application plus grande.

---

## Résumé (tableau à mettre dans le dossier)

| Besoin | Solution « framework » | Mon choix | Pourquoi |
|---|---|---|---|
| Authentification | JWT | **Session PHP** | App rendue côté serveur, déconnexion réelle, rien côté client |
| Organisation du code | MVC (Symfony/Laravel) | **Procédural séparé** | Simplicité, maîtrise, pas de dépendance ; séparation logique respectée |
| Accès base de données | ORM (Doctrine) | **PDO préparé** | Sécurité anti-injection, maîtrise du SQL, aucune surcouche |

**Message clé pour le jury :** ces choix ne sont pas des raccourcis, ce sont des décisions
adaptées à la **taille du projet** et à un **niveau de maîtrise assumé**. Je connais les
alternatives (JWT, MVC, ORM) et je sais **quand** elles deviennent pertinentes.
