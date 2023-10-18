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
 * Defines the Shoutbox entity.
 *
 * @ingroup shoutbox
 *
 * @ContentEntityType(
 *   id = "shoutbox",
 *   label = @Translation("Shoutbox"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\shoutbox\Entity\ListBuilder\ShoutboxListBuilder",
 *     "views_data" = "Drupal\shoutbox\Entity\ViewsData\ShoutboxViewsData",
 *     "form" = {
 *       "default" = "Drupal\shoutbox\Entity\Form\ShoutboxForm",
 *       "add" = "Drupal\shoutbox\Entity\Form\ShoutboxForm",
 *       "edit" = "Drupal\shoutbox\Entity\Form\ShoutboxForm",
 *       "delete" = "Drupal\shoutbox\Entity\Form\ShoutboxDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\shoutbox\Entity\HtmlRouteProvider\ShoutboxHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\shoutbox\Entity\AccessControlHandler\ShoutboxAccessControlHandler",
 *   },
 *   base_table = "shoutbox",
 *   translatable = FALSE,
 *   admin_permission = "administer shoutbox",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uid" = "creator",
 *     "published" = "status",
 *     "owner" = "creator",
 *   },
 *   links = {
 *     "canonical" = "/shoutbox/{shoutbox}",
 *     "add-form" = "/admin/content/shoutbox/add",
 *     "edit-form" = "/admin/content/shoutbox/{shoutbox}/edit",
 *     "delete-form" = "/admin/content/shoutbox/{shoutbox}/delete",
 *     "collection" = "/admin/content/shoutbox",
 *   },
 *   field_ui_base_route = "shoutbox.settings"
 * )
 */
class Shoutbox extends ContentEntityBase implements EntityPublishedInterface, EntityOwnerInterface {

  use EntityChangedTrait, EntityPublishedTrait, EntityOwnerTrait;

  /**
   * Gets the name of the Shoutbox.
   *
   * @return string
   *   The name of the Shoutbox.
   */
  public function getName(): string {
    return $this->get('name')->value;
  }

  /**
   * Sets the name of the Shoutbox.
   *
   * @param string $name
   *   The name of the Shoutbox.
   *
   * @return static
   *   The caller entity itself.
   */
  public function setName(string $name): static {
    $this->set('name', $name);
    return $this;
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
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'creator' => self::getDefaultEntityOwner(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += self::publishedBaseFieldDefinitions($entity_type);
    $fields += self::ownerBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Shoutbox'))
      ->setSetting('max_length', 255)
      ->setSetting('text_processing', 0)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
