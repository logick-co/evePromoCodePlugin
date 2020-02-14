<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Constraint_IsType as PHPUnit_IsType;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;

/**
 * Defines application features from the specific context.
 */
class EveApiPromoCodeContext extends baseEveContext
{
  const URL_CART = '/tck.php/api/v2/carts';
  const URL_LOGIN = '/tck.php/api/v2/login';
  const URL_AUTH = '/tck.php/api/oauth/v2/token?client_id=%s&client_secret=%s&grant_type=password';
  const URL_PAYMENT = '/tck.php/api/v2/checkouts/select-payment/%d';
  const URL_ACTIVATION = self::URL_CART . '/%d/promocodes/activate';
  const URL_SHOW_CART = self::URL_CART. '/%d';
  const CAMPAIGN_NAME = 'PROMOCODES-TEST';
  const CAMPAIGN_TITLE = 'Campaign to test the api';
  const EMAIL = 'tahani@test';
  
  private $client;
  private $token;
  private $cart_id;
  private $code;
  private $contact;
  private $error_message;

  public function __construct()
  {
    parent::__construct();
    
    $uri = $this->getFixtures('base_uri');
    
    $this->client = new Client(['base_uri' => $uri]);
  }

  /**
   * @Given une OP est créée avec :count code
   */
  public function uneOpEstCreeeAvecCode($count)
  {
    $this->createCampaign(self::CAMPAIGN_NAME, self::CAMPAIGN_TITLE);
    
    $this->codes = $this->getCampaignService()->generateCodes($this->campaigns[0], 6, $count);
    $this->campaigns[0]->PromoCodes = $this->codes;
    $this->campaigns[0]->save();
  }

  /**
   * @Given l'api est initialisée
   */
  public function lapiEstInitialisee()
  {
    $user = $this->getFixtures('api_auth');
    
    $data = $this->getRouteData(sprintf(self::URL_AUTH, $user['client_id'], $user['client_secret']));
    
    $this->assertArrayHasKey('access_token', $data);
    $this->assertRegExp('/[0-9a-f]{32}/', $data['access_token']);
    
    $this->token = $data['access_token'];
  }

  /**
   * @Given je m'identifie
   */
  public function jeMidentifie()
  {
    $post_data = [
      'email' => $this->contact->email,
      'password' => $this->contact->password
    ];
    
    try {
      $response = $this->postRouteData(self::URL_LOGIN, $post_data);
    } catch(ServerException $se) {
      $this->error_message = $this->getErrorMessage($se);
    }
    
  }

  /**
   * @Given un contact existe
   */
  public function unContactExiste()
  {
    $contact = Doctrine::getTable('Contact')->findOneByEmail(self::EMAIL);
    
    if ( !$contact )
    {
      $contact = new Contact();
      $contact->name = self::EMAIL;
      $contact->email = self::EMAIL;
      $contact->password = self::EMAIL;
      $contact->save();
    }
    
    $this->contact = $contact;
  }


  /**
   * @When je me connecte à yoot
   */
  public function jeMeConnecteAYoot()
  {
    $data = $this->getRouteData(self::URL_CART);
    
    $embed = $this->extractNode('_embedded', $data);
    $cart = $this->extractNode('items', $embed)[0];

    $this->cart_id = $this->extractNode('id', $cart);
  }
  
  /**
   * @When j'active un code sans être identifié
   */
  public function jactiveUnCodeSansEtreIdentifie()
  {
    $post_data = [
      'promocode' => 'TEST'
    ];
    
    try {
      $response = $this->postRouteData(sprintf(self::URL_ACTIVATION, $this->cart_id), $post_data);
    } catch(ServerException $se) {
      $this->error_message = $this->getErrorMessage($se);
    }
  }
  
  /**
   * @When j'active un code invalide
   */
  public function jactiveUnCodeInvalide()
  {
    $post_data = [
      'promocode' => 'TEST'
    ];
    
    try {
      $response = $this->postRouteData(sprintf(self::URL_ACTIVATION, $this->cart_id), $post_data);
    } catch(ClientException $se) {
      $this->error_message = $this->getErrorMessage($se);
    }
  }

  /**
   * @When j'active un code valide
   */
  public function jactiveUnCodeValide()
  {
    $post_data = [
      'promocode' => $this->codes[0]->code
    ];
    
    try {
      $response = $this->postRouteData(sprintf(self::URL_ACTIVATION, $this->cart_id), $post_data);
      $this->error_message = $this->getPostResponse($response);
    } catch(ServerException $se) {
      $this->error_message = $this->getErrorMessage($se);
    }
  }

  /**
   * @When je valide mon panier
   */
  public function jeValideMonPanier()
  {
    $post_data = [
      'method_id' => $this->getFixtures('payment_method_id')
    ];
    
    try {
      $response = $this->postRouteData(sprintf(self::URL_PAYMENT, $this->cart_id), $post_data);
      $data = json_decode($response, true);
      $method = $this->extractNode('methods', $data);
      $this->error_message = array_keys($method)[0];
    } catch(ServerException $se) {
      $this->error_message = $this->getErrorMessage($se);
    }
  }

  /**
   * @Then la carte est activée
   */
  public function laCarteEstActivee()
  {
    $card = Doctrine::getTable('MemberCard')->findOneByTransactionId($this->cart_id);
    
    $this->assertTrue($card->active);
  }

  /**
   * @Then un abonnement se trouve dans mon panier
   */
  public function unAbonnementSeTrouveDansMonPanier()
  {
    $data = $this->getRouteData(sprintf(self::URL_SHOW_CART, $this->cart_id));
    $items = $this->extractNode('items', $data);
    $this->assertCount(1, $items);
    
    $price = $this->extractNode('price', $items[0]);
    $id = $this->extractNode('id', $price);
    // actually check if the price of the product is the same as the price in the campaign as there is no member card in the cart
    $this->assertEquals($id, $this->campaigns[0]->card_price_id);
  }

  /**
   * @Then le code est activé
   */
  public function leCodeEstActive()
  {
    $code = Doctrine::getTable('PromoCode')->findOneById($this->codes[0]->id);
    
    $this->assertEquals($this->contact->id, $code->account_id);
  }
  
  /**
   * @Then un panier m'est attribué
   */
  public function unPanierMestAttribue()
  {
    $this->assertInternalType(PHPUnit_IsType::TYPE_INT, $this->cart_id);
  }

  /**
   * @Then le message :arg1 est renvoyé
   */
  public function leMessageEstRenvoye($arg1)
  {
    $this->assertEquals($arg1, $this->error_message);
  }

  /*** PRIVATE ***/

  private function getRouteData($route)
  {
    $request = [
      RequestOptions::HEADERS => $this->getHeaders()
    ];
    
    $response = $this->client->get($route, $request);
    
    return $this->getResponse($response);
  }

  private function postRouteData($route, $data)
  {
    $request = [
      RequestOptions::HEADERS => $this->getHeaders(),
      RequestOptions::JSON => $data
    ];
    
    $response = $this->client->post($route, $request);
    
    return $response->getBody()->getContents();
  }
  
  private function getHeaders()
  {
    $headers = [
      'Authorization' => 'Bearer ' . $this->token,
      'Content-Type' => 'application/json'
    ];
    
    return $headers;
  }

  private function getResponse($response)
  {
    $this->assertEquals(200, $response->getStatusCode());
    
    $data = json_decode($response->getBody(), true);
    
    return $data;
  }

  private function getBadResponse($response)
  {
    $data = json_decode($response->getBody(), true);
    
    return $data;
  }

  private function getPostResponse($response)
  {
    $data = json_decode($response, true);
    
    return $this->extractNode('message', $data);
  }

  private function getErrorMessage($se)
  {
    $error = $this->getBadResponse($se->getResponse());
    
    return $this->extractNode('message', $error);
  }

  private function extractNode($name, $root)
  {
    $this->assertArrayHasKey($name, $root);
    
    return $root[$name];
  }

}
