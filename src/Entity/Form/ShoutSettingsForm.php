<?php

declare(strict_types = 1);

namespace Drupal\shoutbox\Entity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for Shout entities.
 *
 * @ingroup shoutbox
 */
class ShoutSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'shout_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Empty implementation of the abstract submit class.
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['shout_settings']['#markup'] = 'Settings form for Shout entities. Manage field settings here.';
    return $form;
  }

}
