<?php

namespace Drupal\random_quotes\Service;

use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Finder\Finder;
use Drupal\random_quotes\Quote;
use Symfony\Component\Serializer\{
  Serializer, 
  Normalizer\ObjectNormalizer, 
  Normalizer\ArrayDenormalizer, 
  Encoder\JsonEncoder
};

class PickRandomQuote implements PickRandomQuoteInterface {

  public function __construct(
    private AccountInterface $account
  ) { }

  public function pick(int $fixedQuote = 0): Quote {
    
    $key = $fixedQuote;
    
    $serializer = new Serializer([new ObjectNormalizer(), new ArrayDenormalizer()], [new JsonEncoder()]);
    
    $finder = new Finder();
    $finder
      ->files()
      ->name('quotes.json')
      ->in(dirname(__FILE__) . '/../..');
    
    foreach ($finder as $file) {
      
      $quotes = $serializer->deserialize($file->getContents(), Quote::class . '[]', 'json');
      
      if ($this->account->hasPermission('show random quote')) {
        $key = mt_rand(0, count($quotes) - 1);
      }
      break;
    }
    
    return $quotes[$key];
  }
}
