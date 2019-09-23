=========================
Codes promo pour le CROUS
=========================

Contexte
--------

*Le client souhaite pouvoir proposer des "bons" physiques sur papier dans des sacs de bienvenue qui seront donnés aux étudiants. En saisissant les "codes promos" inscrits sur ces bons, l'adhésion, qui d'habitude est à 9e, serait offerte.*

*Le client s'occupe de la fabrication des bons, il veut juste que notre système lui fournisse les codes à mettre dessus, dans une grande liste au format csv.*

Besoin
------

Des bons physiques (*papier, carton, plastique...*) sont distribués aux étudiants afin de leur offrir l'abonnement à Yoot. Ces bons contiennent un code qui devra être saisi sur le site web de Yoot. Ce code donnera un abonnement Yoot à l'étudiant connecté.

Environnement
-------------

Bien qu'e-venement possède une gestion de codes promotionnels et de bons cadeaux, certaines fonctionnalités manquent, comme la génération de codes uniques. Il est convenu de développer un module séparé, pour s'affranchir des problèmes de stabilité et de régression inhérents à l'évolution de l'application.

Les fonctionnalités toucheront le front-end, le back-end, l'api et le module de comptabilité si possible. L'ajout de nouvelles tables obligera à créer une nouvelle version 2.14. Une autre solution serait de créer un plugin, comme pour l'api. Cela évitera de modifier la structure d'e-venement et de créer une nouvelle version majeure. Seule la configuration du client sera impactée. Actuellement l'instance du CROUS est en v2.12 et possède un plugin pour gérer l'api.

Pour développer ce module proprement et en prévision de futurs modules, un autre plugin a été créé pour exécuter les tests au format Gherkins. Ce format étant supporté dans de nombreux langages, tout le travail de conception pourra être utilisé directement dans eve3.

Fonctionnalités
---------------

- Création d'un plugin englobant le module
- Création d'une page permettant de consulter les opérations précédentes et de créer une nouvelle opération avec génération des codes
- Ajout d'une route dans l'api pour activer un code promo
- Intégration de l'utilisation des codes dans la comptabilité d'e-venement

Contraintes
***********

- Génération d'une liste de codes
    - Chaque code est unique
    - Le code est à usage unique
    - Les codes sont regroupés dans une opération
    - Les codes d'une opération possèdent une date de validité
    - Données en entrée :
        - Nom de l'opération
        - Date de validité
        - Nombre de codes à générer (minimum de 1 à 500)
    - Données en sortie :
        - La liste des codes au format CSV
    - Format des codes YYY-XXX-000
        - YYY : Code du client (ex: YOOT)
        - XXX : Nom de l'opération (ex: noel2019)
        - 000 : Numéro aléatoire

- La saisie du code dans l'api ajoute un abonnement
    - directement au contact connecté ?
    - dans le panier à 0€ ?
    - dans le panier à 9€ avec un paiement "code promo(campagne)" de 9€ ?

- Consultation de l'utilisation des codes
    - Pour chaque opération, afficher le total de codes, le total de codes utilisés et la possibilité de télécharger la liste au format CSV
    - Liste des codes utilisés ou non et par quel contact

- Affichage en comptabilité
    - Quelle page ? (livre de caisse, livre de vente, journal détaillé)
    - Pour afficher le nom de l'opération ici, il faudra modifier la page

Questions
*********

La nouvelle page apparaît dans le menu "Paramétrage/Extras" avec l'autre page de configuration de l'api.

Des permissions spécifiques seront ajoutées pour créer des campagnes.

L'abonnement étant gratuit, un tarif supplémentaire à 0€ devra être créé dans le produit correspondant.


Glossaire
---------

Bon papier
**********

Carte imprimée donnée aux étudiants dans des sacs de bienvenue. Ces cartes contiennent un code promo offrant une adhésion à l'étudiant.

Etudiant
********

Personne inscrite dans un établissement d’enseignement supérieur et possédant un numéro INE.

Code promo (*Promo Code*)
*************************

Code unique généré par l'application pour permettre à l'étudiant d'adhérer gratuitement à Yoot.

Adhérent
********

Etudiant possédant une adhésion à Yoot.

Adhésion
********

Carte avantage au sein d'e-venement donnant accès aux offres de Yoot. Cette adhésion est normalement payante.

Date de validité
****************

Une OP possède une date de fin à laquelle les codes ne sont plus utilisables.

Opération Promotionnelle (*Campaign*)
*************************************

Regroupement de codes sous la même dénomination. Une OP possède une date de validité et un nombre de codes.

Dénomination
************

Nom d'une OP.

Responsable des OP
******************

Personne chargée de créer les OP et de générer les codes pour les transmettre à l'impression.

Yoot
****

Dispositif culturel à l'initiative du CROUS de Montpellier, succédant au Pass'Culture et proposant des services et offres culturelles aux adhérents.

Code utilisé
************

Lorsqu'un étudiant s'insrit à Yoot à l'aide d'un code promo, ce code ne peut plus être utilisé à nouveau.

Paiement par code
*****************

Lorsqu'un code promo est utilisé pour ajouter une adhésion, le paiement est réalisé par celui-ci. Ce paiement doit apparaître dans le livre de caisse d'e-venement avec la dénomination de l'OP contenant le code promo.

API e-venement
**************

Le site Yoot repose sur l'api d'e-venement. Les codes seront générés depuis le back office d'e-venement mais leur activation se fera depuis le site Yoot, à travers l'api.

Scénarios
---------

Les scénarios suivants sont au format Gherkins utilisé dans le Behaviour Driven Development et permettant d'avoir des tests basés sur des scénarios réels. Ces tests servent de base de développement du module mais également de tests de non régression. Ils permettent aux experts métier de valider les règles avant qu'elles soient implémentées. Ils font aussi office de documentation puisqu'ils sont compréhensibles par les non techniciens.

Les scénarios se trouvent dans les fichiers :

::
  
  plugins/evePromoCodePlugin/features/evePromoCode.feature
  plugins/evePromoCodePlugin/features/eveApiPromoCode.feature

Pour lancer les tests des scénarios, utiliser la commande à la racine de l'instance :

::
  
  plugins/behat4eve2Plugin/vendor/bin/behat --config plugins/evePromoCodePlugin/config/behat.yml
  
Pour lancer un scénario seul, utiliser le tag du scénario :

::

  plugins/behat4eve2Plugin/vendor/bin/behat --config plugins/evePromoCodePlugin/config/behat.yml --tags @impossible

Modèle
------

.. image:: img/PromoCodes.png
   :align: center

- Une opération peut offrir jusqu'à une seule adhésion. Une adhésion pourra être offerte dans plusieurs opérations.
- Une opération peut avoir une date d'expiration.
- Un code appartient à une seul campagne. Une campagne contient plusieurs codes.
- Un code est lié à un seul étudiant. Un étudiant pourra utiliser plusieurs codes.

Documentation de l'API
----------------------

La fonctionnalité est découpée en 4 parties :

- Création d'une campagne avec génération des codes promo
- Utilisation d'un code promo par un étudiant
- Consultation de la campagne et de l'utilisation des codes promo
- Consultation comptable des abonnements obtenus par code promo

La partie qui concerne l'api est l'utilisation d'un code promo par l'étudiant sur le site YOOT.

L'url utilisée pour activer un code promo  est au format 

::
  
  http://e-venement/api/v2/carts/{cartId}/promocodes/activate

Exemple :

::
  
  curl http://e-venement/tck.php/api/v2/carts/{cartId}/promocodes/activate
    -H "Authorization: Bearer 624bd006224c6c31d140b7191a01f8ac" 
    -H "Content-Type: application/json" 
    -X POST
    --data '
    {
      "promocode": "TEST-CODE-56FE78"
    }
    '

La réponse sera au format json suivant en cas d'échec :

::

  {
    "code": 400,
    "message": "Activation Failed",
    "errors": {
      "Invalid code"
    }
  }

en cas de réussite :

::

  {
    "code": 200,
    "message": "Activation Succeeded",
    "errors": { }
  }
  
En cas de réussite, un abonnement au tarif CodePromo à 0€ sera ajouté au panier. Pour finaliser l'activation de l'abonnement, le panier doit être validé normalement, même s'il ne contient que l'abonnement gratuit.;
