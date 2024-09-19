<?php

namespace Drupal\bookingsystem_autoecole;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Bks autoecole heures entities.
 *
 * @ingroup bookingsystem_autoecole
 */
class BksAutoecoleHeuresListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Bks autoecole heures ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeures $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.bks_autoecole_heures.edit_form',
      ['bks_autoecole_heures' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
