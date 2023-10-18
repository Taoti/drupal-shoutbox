<?php

declare(strict_types = 1);

namespace Drupal\shoutbox;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\shoutbox\Entity\Shoutbox;

/**
 * The shoutbox helper service implementation.
 */
class ShoutboxService {

  /**
   * The shout entity storage service instance.
   */
  private readonly EntityStorageInterface $shoutStorage;

  /**
   * The shoutbox entity storage service instance.
   */
  private readonly EntityStorageInterface $shoutboxStorage;

  /**
   * Constructs a new ShoutboxService object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $shoutStorage
   *   The shout entity storage service instance.
   * @param \Drupal\Core\Entity\EntityStorageInterface $shoutboxStorage
   *   The shoutbox entity storage service instance.
   */
  public function __construct(EntityStorageInterface $shoutStorage, EntityStorageInterface $shoutboxStorage) {
    $this->shoutStorage = $shoutStorage;
    $this->shoutboxStorage = $shoutboxStorage;
  }

  /**
   * Gets the shouts of the shoutbox.
   *
   * @param \Drupal\shoutbox\Entity\Shoutbox $shoutbox
   *   The shoutbox to get the shots from.
   * @param int $range
   *   The maximum number of rows to return.
   * @param int $offset
   *   The first record from the result set to return.
   *
   * @return \Drupal\shoutbox\Entity\Shout[]
   *   The selected shouts of the shoutbox.
   */
  public function getShoutboxShouts(Shoutbox $shoutbox, int $range = 20, int $offset = 0): array {
    $query = $this->shoutStorage->getQuery()->accessCheck(FALSE);
    $query->condition('status', TRUE);
    $query->condition('shoutbox', $shoutbox->id());
    $query->range($offset, $range);
    $query->sort('created', 'DESC');
    $shouts_ids = $query->execute();

    return $this->shoutStorage->loadMultiple($shouts_ids);
  }

  /**
   * Gets the count of the shots related to the shoutbox.
   *
   * @param \Drupal\shoutbox\Entity\Shoutbox $shoutbox
   *   The shoutbox to get the shots count from.
   *
   * @return int
   *   The number of related shouts.
   */
  public function getShoutboxNumberOfShouts(Shoutbox $shoutbox): int {
    $query = $this->shoutStorage->getQuery()->accessCheck(FALSE);
    $query->condition('shoutbox', $shoutbox->id());

    return $query->count()->execute();
  }

  /**
   * Gets a list of all enabled shoutboxes.
   *
   * @return string[]
   *   The list of the shoutbox labels, sorted by name, keyed by the id.
   */
  public function getShoutboxAsArray(): array {
    $query = $this->shoutboxStorage->getQuery()->accessCheck(FALSE);
    $query->condition('status', TRUE);
    $query->sort('name');
    $shoutboxes_ids = $query->execute();
    $shoutboxes = $this->shoutboxStorage->loadMultiple($shoutboxes_ids);
    $shoutboxes_array = [];

    foreach ($shoutboxes as $shoutbox) {
      $shoutboxes_array[$shoutbox->id()] = $shoutbox->label();
    }

    return $shoutboxes_array;
  }

}
