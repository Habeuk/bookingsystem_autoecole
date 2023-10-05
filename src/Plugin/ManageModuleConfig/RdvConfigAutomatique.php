<?php

namespace Drupal\bookingsystem_autoecole\Plugin\ManageModuleConfig;

use Drupal\manage_module_config\ManageModuleConfigPluginBase;
use Drupal\Core\Url;

/**
 * Gestion du menu.
 *
 * @ManageModuleConfig(
 *   id = "rdv_config_automatique",
 *   label = @Translation("Rdv Config boite Automatique"),
 *   description = @Translation("Foo description.")
 * )
 */
class RdvConfigAutomatique extends ManageModuleConfigPluginBase {
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigInterface::GetName()
   */
  public function GetName() {
    return $this->configuration['name'];
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigInterface::getRoute()
   */
  public function getRoute() {
    return Url::fromRoute('bookingsystem_autoecole.config.default.auto', [], []);
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigInterface::getDescription()
   */
  public function getDescription() {
    return $this->configuration['description'];
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\manage_module_config\ManageModuleConfigPluginBase::defaultConfiguration()
   */
  public function defaultConfiguration() {
    return [
      'name' => 'RDV boite automatique',
      'description' => "Configurer la prise de rendez-vous pour la boite automatique",
      'enable' => false,
      'icon_svg_class' => 'btn-info text-white btn-lg',
      'icon_svg' => '<svg class="svg-icon" style="width: 1em; height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M758.591876 728.882012h75.264991c17.502081 0 31.690522 14.188441 31.690522 31.690522 0 17.502081-14.188441 31.690522-31.690522 31.690522H726.901354c-17.502081 0-31.690522-14.188441-31.690522-31.690522 0-2.558019 0.303041-5.044735 0.87545-7.427466A31.77371 31.77371 0 0 1 695.210832 745.717602V640.742747c0-17.502081 14.188441-31.690522 31.690522-31.690523 17.502081 0 31.690522 14.188441 31.690522 31.690523v88.139265z m59.419729-260.45648c-17.502081 0-31.690522-14.188441-31.690522-31.690522V135.675048a7.922631 7.922631 0 0 0-7.92263-7.92263H134.68472a7.922631 7.922631 0 0 0-7.922631 7.92263v744.727273a7.922631 7.922631 0 0 0 7.922631 7.922631h286.205029c17.502081 0 31.690522 14.188441 31.690522 31.690522 0 17.502081-14.188441 31.690522-31.690522 31.690522H95.071567c-17.502081 0-31.690522-14.188441-31.690523-31.690522V96.061896c0-17.502081 14.188441-31.690522 31.690523-31.690523h722.940038c17.502081 0 31.690522 14.188441 31.690523 31.690523v340.673114c0 17.502081-14.188441 31.690522-31.690523 31.690522zM208.959381 268.37911h466.444874c17.502081 0 31.690522 14.188441 31.690523 31.690522 0 17.502081-14.188441 31.690522-31.690523 31.690523H208.959381c-17.502081 0-31.690522-14.188441-31.690522-31.690523 0-17.502081 14.188441-31.690522 31.690522-31.690522z m4.951644 204.007737h209.94971c17.502081 0 31.690522 14.188441 31.690522 31.690522 0 17.502081-14.188441 31.690522-31.690522 31.690523H213.911025c-17.502081 0-31.690522-14.188441-31.690522-31.690523 0-17.502081 14.188441-31.690522 31.690522-31.690522z m0 204.007737h209.94971c17.502081 0 31.690522 14.188441 31.690522 31.690522 0 17.502081-14.188441 31.690522-31.690522 31.690523H213.911025c-17.502081 0-31.690522-14.188441-31.690522-31.690523 0-17.502081 14.188441-31.690522 31.690522-31.690522z m301.059961-204.007737h43.574469c17.502081 0 31.690522 14.188441 31.690522 31.690522 0 17.502081-14.188441 31.690522-31.690522 31.690523h-43.574469c-17.502081 0-31.690522-14.188441-31.690522-31.690523 0-17.502081 14.188441-31.690522 31.690522-31.690522z m211.930368 479.319149c-123.608882 0-223.814313-100.205431-223.814313-223.814313s100.205431-223.814313 223.814313-223.814314 223.814313 100.205431 223.814313 223.814314-100.205431 223.814313-223.814313 223.814313z m0-55.458414c92.979992 0 168.355899-75.375907 168.355899-168.355899s-75.375907-168.355899-168.355899-168.3559-168.355899 75.375907-168.355899 168.3559 75.375907 168.355899 168.355899 168.355899z"  /></svg>'
    ] + parent::defaultConfiguration();
  }
  
}
