<?php

namespace Drupal\random_quotes\Plugin\Block;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\random_quotes\Service\PickRandomQuoteInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Show a quote
 * 
 * @Block(
 *   id = "random_quotes_block",
 *   admin_label = @Translation("Random quotes block"),
 *   category = @Translation("Random text")
 * )
 */
class RandomQuotesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    private PickRandomQuoteInterface $pickRandomquote,
    array $configuration, $plugin_id, $plugin_definition
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $container->get(PickRandomQuoteInterface::class),
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  public function build(): array {
    $config = $this->getConfiguration();
    
    return [
      '#theme' => 'random_quotes_block',
      '#quote' => $this->pickRandomquote->pick((int) $config['random_quotes_pick'] ?? 0)
    ];
  }

  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['random_quotes_pick'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fixed quote'),
      '#description' => $this->t('Which fixed quote to show'),
      '#default_value' => $config['random_quotes_pick'] ?? 0,
    ];

    return $form;
  }

  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['random_quotes_pick'] = $values['random_quotes_pick'];
  }
  
  public function getCacheMaxAge() {
    return 60 * 5;
  }
}
