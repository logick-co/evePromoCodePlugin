# language: fr

Fonctionnalité: Génération d'une liste de codes
  En tant que Responsable des OP,
  Je voudrais obtenir une liste de codes promo,
  Dans le but de les imprimer sur carte.

@code
Scénario: Générer un code
  Etant donné qu'aucune OP n'existe 
  Et l'op suivante:
    | name            | expiration | card_type_id | card_price_id |
    | PROMOCODES-TEST | 2019-12-31 | 35           | 8             |
  Lorsqu'un code est généré avec 6 caractères
  Alors le code doit respecter le format "/PROMOCODES-TEST-[0-9A-Z]{6}/"

@codes
Scénario: Générer une liste de codes
  Etant donné qu'aucune OP n'existe 
  Et l'op suivante:
    | name            | expiration | card_type_id | card_price_id |
    | PROMOCODES-TEST | 2019-12-31 | 35           | 8             |
  Lorsque je demande de générer 500 codes de longueur 6
  Alors la liste contient 500 codes
  
@duplicates
Scénario: Générer une liste avec doublons
  Etant donné qu'aucune OP n'existe 
  Et l'op suivante:
    | name            | expiration | card_type_id | card_price_id |
    | PROMOCODES-TEST | 2019-12-31 | 35           | 8             |
  Lorsque je demande de générer 18 codes de longueur 1 
  Alors la liste contient 18 codes
  
@impossible
Scénario: Générer une liste qui ne peut pas être unique
  Etant donné qu'aucune OP n'existe 
  Et l'op suivante:
    | name            | expiration | card_type_id | card_price_id |
    | PROMOCODES-TEST | 2019-12-31 | 35           | 8             |
  Lorsque je demande de générer 50 codes de longueur 1 
  Alors la liste contient 0 code
  Et le message d'erreur est 'Too many attempts to generate unique codes'
