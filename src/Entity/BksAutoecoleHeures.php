<?php

namespace Drupal\bookingsystem_autoecole\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Bks autoecole heures entity.
 *
 * @ingroup bookingsystem_autoecole
 *
 * @ContentEntityType(
 *   id = "bks_autoecole_heures",
 *   label = @Translation("Bks autoecole heures"),
 *   handlers = {
 *     "storage" = "Drupal\bookingsystem_autoecole\BksAutoecoleHeuresStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\bookingsystem_autoecole\BksAutoecoleHeuresListBuilder",
 *     "views_data" = "Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresViewsData",
 *     "translation" = "Drupal\bookingsystem_autoecole\BksAutoecoleHeuresTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\bookingsystem_autoecole\Form\BksAutoecoleHeuresForm",
 *       "add" = "Drupal\bookingsystem_autoecole\Form\BksAutoecoleHeuresForm",
 *       "edit" = "Drupal\bookingsystem_autoecole\Form\BksAutoecoleHeuresForm",
 *       "delete" = "Drupal\bookingsystem_autoecole\Form\BksAutoecoleHeuresDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\bookingsystem_autoecole\BksAutoecoleHeuresHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\bookingsystem_autoecole\BksAutoecoleHeuresAccessControlHandler",
 *   },
 *   base_table = "bks_autoecole_heures",
 *   data_table = "bks_autoecole_heures_field_data",
 *   revision_table = "bks_autoecole_heures_revision",
 *   revision_data_table = "bks_autoecole_heures_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer bks autoecole heures entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *     "bundle" = "booking_config_type",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/bks_autoecole_heures/{bks_autoecole_heures}",
 *     "add-page" = "/admin/structure/bks_autoecole_heures/add",
 *     "add-form" = "/admin/structure/bks_autoecole_heures/add/{booking_config_type}",
 *     "edit-form" = "/admin/structure/bks_autoecole_heures/{bks_autoecole_heures}/edit",
 *     "delete-form" = "/admin/structure/bks_autoecole_heures/{bks_autoecole_heures}/delete",
 *     "version-history" = "/admin/structure/bks_autoecole_heures/{bks_autoecole_heures}/revisions",
 *     "revision" = "/admin/structure/bks_autoecole_heures/{bks_autoecole_heures}/revisions/{bks_autoecole_heures_revision}/view",
 *     "revision_revert" = "/admin/structure/bks_autoecole_heures/{bks_autoecole_heures}/revisions/{bks_autoecole_heures_revision}/revert",
 *     "revision_delete" = "/admin/structure/bks_autoecole_heures/{bks_autoecole_heures}/revisions/{bks_autoecole_heures_revision}/delete",
 *     "translation_revert" = "/admin/structure/bks_autoecole_heures/{bks_autoecole_heures}/revisions/{bks_autoecole_heures_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/bks_autoecole_heures",
 *   },
 *   field_ui_base_route = "entity.booking_config_type.edit_form",
 *   bundle_entity_type = "booking_config_type",
 * )
 */
class BksAutoecoleHeures extends EditorialContentEntityBase implements BksAutoecoleHeuresInterface {
  
  use EntityChangedTrait;
  use EntityPublishedTrait;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id()
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    
    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    
    return $uri_route_parameters;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    // check if booking_config_type is define.
    if (!$this->get('booking_config_type')->target_id)
      throw "La valeur doit etre reatacher une une configuration";
    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);
      
      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }
    
    // If no revision author has been set explicitly,
    // make the bks_autoecole_heures owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }
  
  /**
   * Retourne le nombre de creneaux restant.
   *
   * @return number
   */
  public function getCreneauxLive() {
    return (int) $this->get('creneaux_live')->value;
  }
  
  /**
   * set creneaux_live
   *
   * @param int $nbre_creneaux
   * @return \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures
   */
  public function setCreneauxLive(int $nbre_creneaux) {
    $this->set('creneaux_live', $nbre_creneaux);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    
    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);
    
    $fields['name'] = BaseFieldDefinition::create('string')->setLabel(t('Name'))->setDescription(t('The name of the Bks autoecole heures entity.'))->setRevisionable(TRUE)->setSettings([
      'max_length' => 50,
      'text_processing' => 0
    ])->setDefaultValue('')->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4
    ])->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    
    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')->setLabel(t('Authored by'))->setDescription(t('The user ID of author of the Bks autoecole heures entity.'))->setRevisionable(TRUE)->setSetting('target_type', 'user')->setSetting('handler', 'default')->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'author',
      'weight' => 0
    ])->setDisplayOptions('form', [
      'type' => 'entity_reference_autocomplete',
      'weight' => 5,
      'settings' => [
        'match_operator' => 'CONTAINS',
        'size' => '60',
        'autocomplete_type' => 'tags',
        'placeholder' => ''
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);
    
    $fields['source'] = BaseFieldDefinition::create('list_string')->setLabel(" Source ")->setDisplayOptions('form', [
      'type' => 'options_select'
    ])->setDisplayConfigurable('view', TRUE)->setDisplayConfigurable('form', true)->setSettings([
      'allowed_values' => [
        'order' => 'Commande',
        'manuel' => "Manuel"
      ]
    ])->setRequired(true);
    /**
     * Les heures doivent etre regrouper en fonction du type de conduite.
     * auto ou manulle.
     */
    $fields['type_boite'] = BaseFieldDefinition::create('list_string')->setLabel(" Type de transmission ")->setDisplayOptions('form', [
      'type' => 'options_select'
    ])->setDisplayConfigurable('view', TRUE)->setDisplayConfigurable('form', true)->setSettings([
      'allowed_values' => [
        'automatique' => "Transmission automatique",
        'manuelle' => "Transmission manuelle"
      ]
    ])->setRequired(true);
    
    $fields['owner_heures_id'] = BaseFieldDefinition::create('entity_reference')->setLabel(t('User to whom the hours belong'))->setRevisionable(TRUE)->setSetting('target_type', 'user')->setSetting('handler', 'default')->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'author',
      'weight' => 0
    ])->setDisplayOptions('form', [
      'type' => 'entity_reference_autocomplete',
      'weight' => 5,
      'settings' => [
        'match_operator' => 'CONTAINS',
        'size' => '60',
        'autocomplete_type' => 'tags',
        'placeholder' => ''
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    
    /**
     * Represente le nombre de creneau restants.
     */
    $fields['creneaux_live'] = BaseFieldDefinition::create('integer')->setLabel(t('Number of creneaux remaining'))->setRevisionable(TRUE)->setSettings([
      'min' => 0,
      'suffix' => t('creneau(x)')
    ])->setDefaultValue('')->setDisplayOptions('view', [
      'type' => 'number_integer'
    ])->setDisplayOptions('form', [
      'type' => 'number'
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    
    $fields['commerce_order'] = BaseFieldDefinition::create('entity_reference')->setLabel(t('commerce order'))->setRevisionable(TRUE)->setSetting('target_type', 'commerce_order')->setSetting('handler', 'default')->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'author',
      'weight' => 0
    ])->setDisplayOptions('form', [
      'type' => 'entity_reference_autocomplete',
      'weight' => 5,
      'settings' => [
        'match_operator' => 'CONTAINS',
        'size' => '60',
        'autocomplete_type' => 'tags',
        'placeholder' => ''
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);
    
    $fields['booking_config_type'] = BaseFieldDefinition::create('entity_reference')->setLabel('booking config type')->setSetting('target_type', 'booking_config_type')->setDisplayOptions('view', [])->setDisplayOptions('form', [
      'type' => 'entity_reference_autocomplete',
      'weight' => 5,
      'settings' => [
        'match_operator' => 'CONTAINS',
        'size' => '60',
        'placeholder' => ''
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setSetting('handler', 'default');
    
    $fields['status']->setDescription(t('A boolean indicating whether the Bks autoecole heures is published.'))->setDisplayOptions('form', [
      'type' => 'boolean_checkbox',
      'weight' => -3
    ]);
    
    $fields['created'] = BaseFieldDefinition::create('created')->setLabel(t('Created'))->setDescription(t('The time that the entity was created.'));
    
    $fields['changed'] = BaseFieldDefinition::create('changed')->setLabel(t('Changed'))->setDescription(t('The time that the entity was last edited.'));
    
    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')->setLabel(t('Revision translation affected'))->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))->setReadOnly(TRUE)->setRevisionable(TRUE)->setTranslatable(TRUE);
    
    return $fields;
  }
  
}
