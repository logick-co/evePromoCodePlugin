<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Constraint_IsType as PHPUnit_IsType;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ServerException;

/**
 * Defines application features from the specific context.
 */
class EveApiPromoCodeContext extends baseEveContext
{
  const URL_CART = '/tck_dev.php/api/v2/carts';
  const URL_ACTIVATION = self::URL_CART . '/%d/promocodes/activate';
  const URL_AUTH = '/tck_dev.php/api/oauth/v2/token?client_id=eve&client_secret=eve&grant_type=password';
  
  private $client;
  private $token;
  private $cart_id;
  private $code;
  private $error_message;

  public function __construct()
  {
    parent::__construct();
    
    $this->client = new Client(['base_uri' => 'http://eve/']);
  }

  /**
   * @Given l'api est initialisée
   */
  public function lapiEstInitialisee()
  {
    $data = $this->getRouteData(self::URL_AUTH);
    
    $this->assertArrayHasKey('access_token', $data);
    $this->assertRegExp('/[0-9a-f]{32}/', $data['access_token']);
    
    $this->token = $data['access_token'];
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
   * @Then un panier m'est attribué
   */
  public function unPanierMestAttribue()
  {
    $this->assertInternalType(PHPUnit_IsType::TYPE_INT, $this->cart_id);
  }

  /**
   * @Given je possède un code
   */
  public function jePossedeUnCode()
  {
    $this->code = 'TEST';
  }

  /**
   * @When j'active un code
   */
  public function jactiveUnCode()
  {
    $post_data = [
      'promocode' => $this->code
    ];
    
    try {
      $response = $this->postRouteData(sprintf(self::URL_ACTIVATION, $this->cart_id), $post_data);
    } catch(ServerException $se) {
      $error = $this->getBadResponse($se->getResponse());
      
      $this->error_message = $this->extractNode('message', $error);
    }
  }

  /**
   * @Then le message :arg1 est renvoyé
   */
  public function leMessageEstRenvoye($arg1)
  {
    $this->assertEquals($arg1, $this->error_message);
  }

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

  private function extractNode($name, $root)
  {
    $this->assertArrayHasKey($name, $root);
    
    return $root[$name];
  }

}
