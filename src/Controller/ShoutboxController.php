<?php

declare(strict_types = 1);

namespace Drupal\shoutbox\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\shoutbox\Entity\Shoutbox;
use Drupal\shoutbox\ShoutboxService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * The shoutbox controller implementation.
 */
class ShoutboxController extends ControllerBase {

  /**
   * The shoutbox helper service instance.
   */
  private readonly ShoutboxService $shoutboxService;

  /**
   * The entity view builder service instance.
   */
  private readonly EntityViewBuilderInterface $viewBuilder;

  /**
   * The Drupal Renderer service instance.
   */
  private readonly RendererInterface $renderer;

  /**
   * Constructs a new ShoutboxController object.
   *
   * @param \Drupal\shoutbox\ShoutboxService $shoutbox_service
   *   The shoutbox helper service instance.
   * @param \Drupal\Core\Entity\EntityViewBuilderInterface $shoutViewBuilder
   *   The shout entity view builder service instance.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The Drupal renderer service instance.
   */
  public function __construct(ShoutboxService $shoutbox_service, EntityViewBuilderInterface $shoutViewBuilder, RendererInterface $renderer) {
    $this->shoutboxService = $shoutbox_service;
    $this->viewBuilder = $shoutViewBuilder;
    $this->renderer = $renderer;
  }

  /**
   * Loads all enabled shouts.
   *
   * @param \Drupal\shoutbox\Entity\Shoutbox $shoutbox
   *   The shoutbox to get the shots from.
   * @param int $range
   *   The maximum number of rows to return.
   * @param int $offset
   *   The first record from the result set to return.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The selected shouts of the shoutbox.
   */
  public function loadShouts(Shoutbox $shoutbox, int $range = 20, int $offset = 0): JsonResponse {
    $shouts = $this->shoutboxService->getShoutboxShouts($shoutbox, $range, $offset);
    $data = [
      'shouts' => [],
      'has_mode_shouts' => $this->shoutboxService->getShoutboxNumberOfShouts($shoutbox) > $offset + $range,
    ];
    foreach ($shouts as $shout) {
      $shoutArray = $this->viewBuilder->view($shout);
      $data['shouts'][] = $this->renderer->render($shoutArray);
    }

    return new JsonResponse($data);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('shoutbox.service'),
      $container->get('shoutbox.viewbuilder.shout'),
      $container->get('renderer')
    );
  }

}
