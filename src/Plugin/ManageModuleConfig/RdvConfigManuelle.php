<?php

namespace Drupal\bookingsystem_autoecole\Plugin\ManageModuleConfig;

use Drupal\manage_module_config\ManageModuleConfigPluginBase;
use Drupal\Core\Url;

/**
 * Gestion du menu.
 *
 * @ManageModuleConfig(
 *   id = "rdv_config_manuelle",
 *   label = @Translation("Rdv Config boite Manuelle"),
 *   description = @Translation("Foo description.")
 * )
 */
class RdvConfigManuelle extends ManageModuleConfigPluginBase {
  
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
    return Url::fromRoute('bookingsystem_autoecole.config.default', [], []);
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
      'name' => 'RDV boite manuelle',
      'description' => "Configurer la prise de rendez-vous pour la boite manuelle",
      'enable' => false,
      'icon_svg_class' => 'btn-danger text-white btn-lg',
      'icon_svg' => '<svg class="svg-icon" style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M883.456 426.02496V244.15744c0-85.29408-69.12512-154.42432-154.42432-154.42432h-16.67072v-36.93568c0-21.28896-17.26464-38.5536-38.62016-38.5536a38.5536 38.5536 0 0 0-38.5536 38.5536v36.93568H274.88768v-36.93568c0-21.28896-17.3312-38.5536-38.62016-38.5536-21.36064 0-38.62016 17.26464-38.62016 38.5536v36.93568H162.8672c-85.2992 0-154.42432 69.13024-154.42432 154.42432v591.89248c0 85.29408 69.12512 154.4192 154.42432 154.4192h293.72928a38.58432 38.58432 0 0 0 49.8944-36.93568c0-19.05152-13.83424-34.8928-32.04608-38.02112a9.10336 9.10336 0 0 0-1.35168-0.59904c-11.72992-3.81952-310.2208-1.68448-310.2208-1.68448-42.57792 0-77.17376-34.60096-77.17376-77.17888v-410.0608h768.19968M162.8672 166.90688h34.78016v66.03264a38.58432 38.58432 0 0 0 38.62016 38.62016 38.6304 38.6304 0 0 0 38.62016-38.62016V166.90688h360.29952v66.03264c0 21.36064 17.26464 38.62016 38.5536 38.62016a38.58432 38.58432 0 0 0 38.62016-38.62016V166.90688h16.67072c42.57792 0 77.24544 34.66752 77.24544 77.24544v104.58624H85.68832V244.15744c0-42.57792 34.60096-77.25056 77.17888-77.25056z"  /><path d="M763.81184 477.44c-142.13632 0-257.32608 115.2512-257.32608 257.32608 0 142.13632 115.18464 257.39264 257.32608 257.39264 142.13632 0 257.39264-115.2512 257.39264-257.39264 0-142.07488-115.2512-257.32608-257.39264-257.32608z m0 437.46816c-99.28192 0-180.1472-80.7936-180.1472-180.1472 0-99.29216 80.86016-180.08064 180.1472-180.08064 99.3536 0 180.1472 80.78848 180.1472 180.08064 0 99.3536-80.78848 180.1472-180.1472 180.1472z"  /><path d="M841.05728 760.55552h-77.24544V683.3152a25.74848 25.74848 0 0 0-25.72288-25.73312 25.7536 25.7536 0 0 0-25.728 25.73312v102.96832a25.74336 25.74336 0 0 0 25.728 25.728h102.96832a25.74848 25.74848 0 0 0 25.728-25.728 25.74848 25.74848 0 0 0-25.728-25.728zM352.06656 528.89088h-154.4192a25.75872 25.75872 0 0 0 0 51.51744h154.42432a25.74336 25.74336 0 0 0 25.72288-25.728 25.75872 25.75872 0 0 0-25.728-25.78944zM352.06656 709.03296h-154.4192a25.76384 25.76384 0 0 0-25.728 25.79968 25.74336 25.74336 0 0 0 25.728 25.72288h154.42432a25.73824 25.73824 0 0 0 25.72288-25.72288 25.76384 25.76384 0 0 0-25.728-25.79968z"  /></svg>'
    ] + parent::defaultConfiguration();
  }
  
}
