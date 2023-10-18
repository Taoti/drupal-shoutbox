<?php

/**
 * @file
 * Contains shoutbox.page.inc.
 *
 * Page callback for Shoutbox entities.
 */

declare(strict_types = 1);

use Drupal\Core\Render\Element;
use Drupal\shoutbox\Entity\Shout;

/**
 * Prepares variables for Shoutbox templates.
 *
 * Default template: shoutbox.html.twig.
 *
 * @param mixed[] $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_shoutbox(array &$variables): void {
  $nb_shouts_to_display = 2;
  $nb_shouts_to_reload = 20;
  $variables['range'] = $nb_shouts_to_reload;
  $variables['offset'] = $nb_shouts_to_display;
  $variables['#attached']['library'][] = 'shoutbox/shoutbox';

  // Fetch Shoutbox Entity Object.
  $shoutbox = $variables['elements']['#shoutbox'];
  $variables['shoutbox_id'] = $shoutbox->id();
  $variables['attributes']['class'][] = 'shoutbox-' . $shoutbox->id();
  $variables['attributes']['class'][] = 'js-shoutbox-' . $shoutbox->id();

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }

  /** @var \Drupal\shoutbox\ShoutboxService $shoutbox_service */
  $shoutbox_service = \Drupal::service('shoutbox.service');

  $variables['content']['shouts'] = [];
  $shouts = $shoutbox_service->getShoutboxShouts($shoutbox, $nb_shouts_to_display);
  $variables['nb_shouts'] = $shoutbox_service->getShoutboxNumberOfShouts($shoutbox);
  $variables['has_mode_shouts'] = $variables['nb_shouts'] > $nb_shouts_to_display;
  $viewBuilder = \Drupal::service('shoutbox.viewbuilder.shout');

  foreach ($shouts as $shout) {
    $variables['content']['shouts'][] = $viewBuilder->view($shout);
  }

  $variables['form'] = [
    'cache' => [
      'contexts' => ['user.roles'],
    ],
  ];

  if (\Drupal::currentUser()->hasPermission('shoutbox shout')) {
    $shoutNew = Shout::create(['shoutbox' => $shoutbox->id()]);
    $variables['form']['data'] = \Drupal::service('entity.form_builder')->getForm($shoutNew, 'default');
  }

}
