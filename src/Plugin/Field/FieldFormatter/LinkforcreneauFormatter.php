<?php

namespace Drupal\bookingsystem_autoecole\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'LinkForCreneau' formatter.
 *
 * @FieldFormatter(
 *   id = "bookingsystem_autoecole_linkforcreneau",
 *   label = @Translation(" Link For Creneau (auto-ecole) "),
 *   field_types = {
 *     "string",
 *     "link"
 *   }
 * )
 */
class LinkforcreneauFormatter extends FormatterBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'route' => 'bookingsystem_autoecole.page.app',
      'empty_hours' => "Vous n'avez plus d'heure disponible, vous devez acheter un forfait ou des heures supplementaires !!!",
      'no_login' => 'Vous devez etre connecter afin de pouvoir effectuer des reservations'
    ] + parent::defaultSettings();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['route'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Route name'),
      '#description' => "Ce lien est utilisé pour renvoyer l'utilisateur vers la page de connection",
      '#default_value' => $this->getSetting('route')
    ];
    return $elements;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary[] = [];
    return $summary;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $routeName = $this->getSetting('route');
    if (!empty($routeName)) {
      // check user is login.
      $uid = \Drupal::currentUser()->id();
      if ($uid > 0) {
        // check if user have hours.
        /**
         *
         * @var \Drupal\bookingsystem_autoecole\Services\ManagerCreneauxAuto $manager_creneaux
         */
        $manager_creneaux = \Drupal::service("bookingsystem_autoecole.app_manager_creneaux");
        $hours_manuel = $manager_creneaux->getHoursByUser('manuelle', false);
        $hours_auto = $manager_creneaux->getHoursByUser('automatique', false);
        if ($hours_manuel > 0 || $hours_auto > 0) {
          if ($hours_auto > 0) {
            $url = Url::fromRoute($routeName, [
              'type_boite' => 'automatique'
            ]);
            $suffix = '';
            if ($hours_auto > 0 && $hours_manuel > 0)
              $suffix = "( Boite automatique )";
            foreach ($items as $delta => $item) {
              $element[$delta] = [
                '#type' => 'link',
                '#title' => $item->title . $suffix,
                '#options' => [
                  'attributes' => [
                    'class' => [
                      'btn',
                      'btn-primary',
                      'd-block'
                    ]
                  ]
                ]
              ];
              $element[$delta]['#url'] = $url;
            }
          }
          if ($hours_manuel > 0) {
            $suffix = '';
            if ($hours_auto > 0 && $hours_manuel > 0)
              $suffix = "( Boite manuelle )";
            $url = Url::fromRoute($routeName, [
              'type_boite' => 'manuelle'
            ]);
            foreach ($items as $delta => $item) {
              $element[$delta] = [
                '#type' => 'link',
                '#title' => $item->title . $suffix,
                '#options' => [
                  'attributes' => [
                    'class' => [
                      'btn',
                      'btn-primary',
                      'd-block'
                    ]
                  ]
                ]
              ];
              $element[$delta]['#url'] = $url;
            }
          }
        }
        else {
          $element[] = [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => [
              'class' => [
                'alert',
                'alert-warning',
                'p-md-5 font-weight-bold d-flex align-items-center'
              ]
            ],
            [
              '#type' => 'html_tag',
              '#tag' => 'i',
              '#attributes' => [
                'class' => [
                  'fas fa-exclamation-triangle mr-4'
                ]
              ]
            ],
            [
              '#type' => 'html_tag',
              '#tag' => 'span',
              '#attributes' => [
                'class' => []
              ],
              '#value' => $this->getSetting('empty_hours')
            ]
          ];
          
          foreach ($items as $delta => $item) {
            $element[] = [
              '#type' => 'link',
              '#title' => $item->title,
              '#options' => [
                'attributes' => [
                  'class' => [
                    'btn',
                    'btn-primary',
                    'disabled',
                    'd-block'
                  ]
                ]
              ],
              '#url' => Url::fromUserInput('#')
            ];
          }
        }
      }
      else {
        $element[] = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => [
            'class' => [
              'alert',
              'alert-warning',
              'p-md-5 font-weight-bold d-flex align-items-center'
            ]
          ],
          [
            '#type' => 'html_tag',
            '#tag' => 'i',
            '#attributes' => [
              'class' => [
                'fas fa-exclamation-triangle mr-4'
              ]
            ]
          ],
          [
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => [
              'class' => []
            ],
            '#value' => $this->getSetting('no_login')
          ]
        ];
        foreach ($items as $delta => $item) {
          $element[] = [
            '#type' => 'link',
            '#title' => $item->title,
            '#options' => [
              'attributes' => [
                'class' => [
                  'btn',
                  'btn-primary',
                  'disabled',
                  'd-block'
                ]
              ]
            ],
            '#url' => Url::fromUserInput('#')
          ];
        }
        // $element[] = [
        // '#type' => 'link',
        // '#title' => 'Inscription',
        // '#options' => [
        // 'attributes' => [
        // 'class' => [
        // 'btn-link'
        // ]
        // ]
        // ],
        // '#url' => Url::fromRoute('user.login')
        // ];
        
        $element[] = [
          '#type' => 'html_tag',
          '#tag' => 'section',
          '#attributes' => [
            'id' => 'appLoginRegister'
          ],
          '#attached' => [
            'library' => [
              'login_rx_vuejs/login_register_small'
            ]
          ]
        ];
      }
    }
    return $element;
  }
  
}
