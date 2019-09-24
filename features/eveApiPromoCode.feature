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
  
@anonyme
Scénario: Activer un code de manière anonyme
  Etant donné que l'api est initialisée
  Et que je me connecte à yoot
  Lorsque j'active un code sans être identifié
  Alors le message 'You need to be identified, please login' est renvoyé
  
@invalide
Scénario: Activer un code invalide
  Etant donné que l'api est initialisée
  Et qu'une OP est créée avec 0 code
  Et que je me connecte à yoot
  Et qu'un contact existe
  Et que je m'identifie
  Lorsque j'active un code invalide
  Alors le message 'Activation Failed' est renvoyé

@valide
Scénario: Activer un code valide
  Etant donné que l'api est initialisée
  Et qu'une OP est créée avec 1 code
  Et que je me connecte à yoot
  Et qu'un contact existe
  Et que je m'identifie
  Lorsque j'active un code valide
  Alors le message 'Activation Succeeded' est renvoyé
  Et le code est activé
  Et un abonnement se trouve dans mon panier

@paiement
Scénario: Valider le panier
  Etant donné que l'api est initialisée
  Et qu'une OP est créée avec 1 code
  Et que je me connecte à yoot
  Et qu'un contact existe
  Et que je m'identifie
  Et que j'active un code valide
  Lorsque je valide mon panier
  Alors le message 'NothingToPay' est renvoyé
  Et la carte est activée
