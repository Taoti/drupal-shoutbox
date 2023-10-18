<?php

declare(strict_types = 1);

namespace Drupal\shoutbox\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\shoutbox\ShoutboxService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ShoutboxBlock' block.
 *
 * @Block(
 *  id = "shoutbox_block",
 *  admin_label = @Translation("Shoutbox"),
 * )
 */
class ShoutboxBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Shoutbox service instance.
   */
  protected ShoutboxService $shoutbox;

  /**
   * The view builder service instance for the shoutbox entity type.
   */
  protected EntityViewBuilderInterface $viewBuilder;

  /**
   * The shoutbox entity storage service instance.
   */
  protected EntityStorageInterface $shoutboxStorage;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['shoutbox'] = [
      '#type' => 'select',
      '#title' => $this->t('Shoutbox to use'),
      '#default_value' => $this->configuration['shoutbox'],
      '#options' => $this->shoutbox->getShoutboxAsArray(),
      '#weight' => '0',
      'required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['shoutbox'] = $form_state->getValue('shoutbox');
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'view shoutbox');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $shoutbox = $this->shoutboxStorage->load($this->configuration['shoutbox']);
    return $this->viewBuilder->view($shoutbox);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->shoutbox = $container->get('shoutbox.service');
    $instance->viewBuilder = $container->get('shoutbox.viewbuilder.shoutbox');
    $instance->shoutboxStorage = $container->get('shoutbox.storage.shoutbox');

    return $instance;
  }

}
