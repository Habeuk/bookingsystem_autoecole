<?php

namespace Drupal\bookingsystem_autoecole\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Returns responses for bookingsystem_autoecole routes.
 */
class BookingsystemAutoecoleController extends ControllerBase {
  
  /**
   * Builds the response to showing the Vue-js app.
   * On definit les urls pour l'initialisation de l'application.
   */
  public function build($type_boite) {
    if (!$this->currentUser()->id()) {
      $this->messenger()->addWarning($this->t('You must be logged in to make a reservation'));
      $build['content'] = [];
      return $build;
    }
    $booking_config_type_id = $type_boite != "autommatique" ? $this->getEntityConfigId()->id() : $this->getEntityConfigId("automatique", "conduite_auto")->id();
    $urlCalendar = Url::fromRoute("bookingsystem_autoecole.app_load_config_calendar");
    $urlCreneaux = Url::fromRoute("bookingsystem_autoecole.app_load_creneaux", [
      'booking_config_type_id' => $booking_config_type_id,
      'type_boite' => $type_boite,
      'date' => null
    ]);
    $urlSave = Url::fromRoute("bookingsystem_autoecole.save_reservation", [
      'booking_config_type_id' => $booking_config_type_id,
      'type_boite' => $type_boite
    ]);
    $build['content'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      "#attributes" => [
        'id' => 'app',
        'data-url-calendar' => '/' . $urlCalendar->getInternalPath(),
        'data-url-creneaux' => '/' . $urlCreneaux->getInternalPath(),
        'data-url-save' => '/' . $urlSave->getInternalPath(),
        'class' => [
          'p-md-5'
        ]
      ]
    ];
    $build['content']['#attached']['library'][] = 'booking_system/booking_system_app2';
    return $build;
  }
  
  /**
   * Permet de generer et de configurer RDV par domaine pour le type de
   * transmission manuel.
   */
  public function ConfigureDefault() {
    $entityConfig = $this->getEntityConfigId();
    $form = $this->entityFormBuilder()->getForm($entityConfig, "edit", [
      'redirect_route' => 'bookingsystem_autoecole.config_resume',
      'booking_config_type_id' => $entityConfig->id()
    ]);
    return $form;
  }
  
  /**
   * Permet de generer et de configurer RDV par domaine pour les boites
   * Automatique.
   */
  public function ConfigureDefaultBoiteAuto() {
    $entityConfig = $this->getEntityConfigId("auto", "conduite_auto");
    $form = $this->entityFormBuilder()->getForm($entityConfig, "edit", [
      'redirect_route' => 'bookingsystem_autoecole.config_resume',
      'booking_config_type_id' => $entityConfig->id()
    ]);
    return $form;
  }
  
  /**
   * Retrieve the the right booking_config_type
   * si lesroidelareno n'est pas installé alors il essaie de lire la
   * configuration wb_horizon_public.config_auto_ecole
   * si cette config est absente alors il la crée
   */
  protected function getEntityConfigId($type = "manuelle", $config_field = "conduite_manuelle") {
    $entity_type_id = "booking_config_type";
    $entityConfig = null;
    $hasLesroidelareno = \Drupal::moduleHandler()->moduleExists('lesroidelareno');
    if ($hasLesroidelareno) {
      $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->load(\Drupal\lesroidelareno\lesroidelareno::getCurrentPrefixDomain() . ($type == "auto" ? "auto" : ""));
    }
    else {
      /**
       *
       * @var \Drupal\Core\Config\Config $configs
       */
      $configs = \Drupal::service('config.factory')->getEditable('wb_horizon_public.config_auto_ecole');
      if ($configs) {
        $entityConfigid = $configs->get($config_field);
        if ($entityConfigid)
          $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->load($entityConfigid);
      }
    }
    if (!$entityConfig) {
      if ($hasLesroidelareno) {
        $entityConfigId = \Drupal\lesroidelareno\lesroidelareno::getCurrentPrefixDomain() . ($type == "auto" ? "auto" : "");
      }
      else {
        $entityConfigId = 'conduite_' . $type;
      }
      $values = [
        'label' => t('Box slot configuration ') . $type,
        'days' => \Drupal\booking_system\DaysSettingsInterface::DAYS,
        'id' => $entityConfigId
      ];
      $entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->create($values);
      $entityConfig->save();
      if (!$hasLesroidelareno) {
        /**
         * we update the configuration
         *
         * @var \Drupal\Core\Config\Config $configs
         */
        $configs = \Drupal::service('config.factory')->getEditable('wb_horizon_public.config_auto_ecole');
        $configs->set($config_field, $entityConfigId);
        $configs->save();
      }
    }
    return $entityConfig;
  }
}
