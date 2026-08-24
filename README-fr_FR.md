# EASYTOOLTIP POUR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## Fonctionnalités

EasyTooltip enrichit les infobulles que Dolibarr affiche au survol d'un lien
vers un objet (commande, facture, devis, produit, tiers, utilisateur, compte
bancaire, adhérent, ticket, sondage, projet...). Pour chaque type d'objet, il
peut ajouter, en plus du contenu standard de l'infobulle :

- Les notes publique et privée de l'objet.
- Pour les produits/services : la description complète, la durée du service,
  les dernières commandes clients, les dernières commandes fournisseurs, et
  le stock par entrepôt.

Chacun de ces blocs peut être activé ou désactivé indépendamment, par type
d'objet, depuis la page de configuration du module.

Le module fournit également deux pilotes de CAPTCHA supplémentaires,
sélectionnables depuis `Accueil - Configuration - Sécurité - Code captcha` :

- **EasyTooltip** : un CAPTCHA image simple (généré via GD, comme le CAPTCHA
  standard de Dolibarr mais avec des couleurs et une rotation aléatoires).
- **EasyTooltip avancé** : un CAPTCHA à sélection d'image basé sur la
  bibliothèque [IconCaptcha](https://github.com/fabianwennink/IconCaptcha-PHP),
  où l'utilisateur doit cliquer sur l'icône qui apparaît le moins de fois.

Enfin, le module embarque FontAwesome 7 (free) et, une fois activé, bascule
automatiquement le jeu d'icônes de Dolibarr sur cette version (constante
`MAIN_FONTAWESOME_DIRECTORY`), à la place de FontAwesome 5 fourni avec le
cœur de Dolibarr.

D'autres modules externes sont disponibles sur [Dolistore.com](https://www.dolistore.com).

## Traductions

Les traductions peuvent être complétées manuellement en éditant les fichiers
du répertoire *langs*.

## Licences

### Code principal

GPLv3 ou (à votre choix) toute version ultérieure. Voir le fichier COPYING
pour plus d'informations.

### Documentation

Tous les textes et readmes sont sous licence GFDL.
