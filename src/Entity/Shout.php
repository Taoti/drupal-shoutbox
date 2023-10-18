<?php

declare(strict_types = 1);

namespace Drupal\shoutbox\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Shout entity.
 *
 * @ingroup shoutbox
 *
 * @ContentEntityType(
 *   id = "shout",
 *   label = @Translation("Shout"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\shoutbox\Entity\ListBuilder\ShoutListBuilder",
 *     "views_data" = "Drupal\shoutbox\Entity\ViewsData\ShoutViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\shoutbox\Entity\Form\ShoutForm",
 *       "add" = "Drupal\shoutbox\Entity\Form\ShoutForm",
 *       "edit" = "Drupal\shoutbox\Entity\Form\ShoutForm",
 *       "delete" = "Drupal\shoutbox\Entity\Form\ShoutDeleteForm",
 *     },
 *     "access" = "Drupal\shoutbox\Entity\AccessControlHandler\ShoutAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\shoutbox\Entity\HtmlRouteProvider\ShoutHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "shout",
 *   translatable = FALSE,
 *   admin_permission = "administer shoutbox",
 *   entity_keys = {
 *     "id" = "id",
 *     "uid" = "author",
 *     "published" = "status",
 *     "owner" = "author",
 *   },
 *   links = {
 *     "add-form" = "/admin/content/shoutbox/shout/add",
 *     "edit-form" = "/admin/content/shoutbox/shout/{shout}/edit",
 *     "delete-form" = "/admin/content/shoutbox/shout/{shout}/delete",
 *     "collection" = "/admin/content/shoutbox/shout",
 *   },
 *   field_ui_base_route = "shout.settings"
 * )
 */
class Shout extends ContentEntityBase implements EntityPublishedInterface, EntityOwnerInterface {

  use EntityChangedTrait, EntityPublishedTrait, EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function label() {
    $message = $this->getShout();
    return substr(strip_tags($message), 0, 500);
  }

  /**
   * Gets the entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the entity.
   */
  public function getCreatedTime(): int {
    return intval($this->get('created')->value);
  }

  /**
   * Sets the entity creation timestamp.
   *
   * @param int $timestamp
   *   The entity creation timestamp.
   *
   * @return static
   *   The caller entity itself.
   */
  public function setCreatedTime(int $timestamp): static {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * Gets the shoutbox Id.
   *
   * @return int
   *   The shoutbox id.
   */
  public function getShoutboxId(): int {
    return intval($this->get('shoutbox')->target_id);
  }

  /**
   * Gets the shoutbox entity instance.
   *
   * @return \Drupal\shoutbox\Entity\Shoutbox
   *   The shoutbox entity instance.
   */
  public function getShoutbox(): Shoutbox {
    return $this->get('shoutbox')->entity;
  }

  /**
   * Gets the shout text.
   *
   * @return string
   *   The shout text.
   */
  public function getShout(): string {
    return $this->get('shout')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values): void {
    parent::preCreate($storage_controller, $values);
    $values += [
      'author' => self::getDefaultEntityOwner(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += self::publishedBaseFieldDefinitions($entity_type);
    $fields += self::ownerBaseFieldDefinitions($entity_type);

    $fields['shoutbox'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Shoutbox'))
      ->setSetting('target_type', 'shoutbox')
      ->setSetting('handler', 'default')
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['shout'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Shout'))
      ->setSetting('text_processing', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
