<?php

namespace Drupal\bookingsystem_autoecole\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Bks autoecole heures entities.
 */
class BksAutoecoleHeuresViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
