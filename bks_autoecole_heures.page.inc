<?php

/**
 * @file
 * Contains bks_autoecole_heures.page.inc.
 *
 * Page callback for Bks autoecole heures entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Bks autoecole heures templates.
 *
 * Default template: bks_autoecole_heures.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_bks_autoecole_heures(array &$variables) {
  // Fetch BksAutoecoleHeures Entity Object.
  $bks_autoecole_heures = $variables['elements']['#bks_autoecole_heures'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
