# language: fr

Fonctionnalité: Activation d'un code par l'api d'e-venement
  En tant qu'étudiant,
  Je voudrais activer mon code promo,
  Dans le but d'obtenir l'abonnement correspondant.


@activation
Scénario: Accéder à l'api
  Etant donné que l'api est initialisée
  Lorsque je me connecte à yoot
  Alors un panier m'est attribué
  
@anonymous
Scénario: Activer un code de manière anonyme
  Etant donné que l'api est initialisée
  Et que je me connecte à yoot
  Et que je possède un code
  Lorsque j'active un code
  Alors le message 'You need to be identified, please login' est renvoyé
  
  
