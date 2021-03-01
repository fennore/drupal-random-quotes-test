<?php

namespace Drupal\random_quotes\Service;

use Drupal\random_quotes\Quote;

interface PickRandomQuoteInterface {
   public function pick(int $fixedQuote = 0): Quote;
}
