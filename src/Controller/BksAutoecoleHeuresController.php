<?php

namespace Drupal\bookingsystem_autoecole\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BksAutoecoleHeuresController.
 *
 *  Returns responses for Bks autoecole heures routes.
 */
class BksAutoecoleHeuresController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Bks autoecole heures revision.
   *
   * @param int $bks_autoecole_heures_revision
   *   The Bks autoecole heures revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($bks_autoecole_heures_revision) {
    $bks_autoecole_heures = $this->entityTypeManager()->getStorage('bks_autoecole_heures')
      ->loadRevision($bks_autoecole_heures_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('bks_autoecole_heures');

    return $view_builder->view($bks_autoecole_heures);
  }

  /**
   * Page title callback for a Bks autoecole heures revision.
   *
   * @param int $bks_autoecole_heures_revision
   *   The Bks autoecole heures revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($bks_autoecole_heures_revision) {
    $bks_autoecole_heures = $this->entityTypeManager()->getStorage('bks_autoecole_heures')
      ->loadRevision($bks_autoecole_heures_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $bks_autoecole_heures->label(),
      '%date' => $this->dateFormatter->format($bks_autoecole_heures->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Bks autoecole heures.
   *
   * @param \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface $bks_autoecole_heures
   *   A Bks autoecole heures object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(BksAutoecoleHeuresInterface $bks_autoecole_heures) {
    $account = $this->currentUser();
    $bks_autoecole_heures_storage = $this->entityTypeManager()->getStorage('bks_autoecole_heures');

    $langcode = $bks_autoecole_heures->language()->getId();
    $langname = $bks_autoecole_heures->language()->getName();
    $languages = $bks_autoecole_heures->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $bks_autoecole_heures->label()]) : $this->t('Revisions for %title', ['%title' => $bks_autoecole_heures->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all bks autoecole heures revisions") || $account->hasPermission('administer bks autoecole heures entities')));
    $delete_permission = (($account->hasPermission("delete all bks autoecole heures revisions") || $account->hasPermission('administer bks autoecole heures entities')));

    $rows = [];

    $vids = $bks_autoecole_heures_storage->revisionIds($bks_autoecole_heures);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\bookingsystem_autoecole\Entity\BksAutoecoleHeuresInterface $revision */
      $revision = $bks_autoecole_heures_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $bks_autoecole_heures->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.bks_autoecole_heures.revision', [
            'bks_autoecole_heures' => $bks_autoecole_heures->id(),
            'bks_autoecole_heures_revision' => $vid,
          ]))->toString();
        }
        else {
          $link = $bks_autoecole_heures->toLink($date)->toString();
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.bks_autoecole_heures.translation_revert', [
                'bks_autoecole_heures' => $bks_autoecole_heures->id(),
                'bks_autoecole_heures_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.bks_autoecole_heures.revision_revert', [
                'bks_autoecole_heures' => $bks_autoecole_heures->id(),
                'bks_autoecole_heures_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.bks_autoecole_heures.revision_delete', [
                'bks_autoecole_heures' => $bks_autoecole_heures->id(),
                'bks_autoecole_heures_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['bks_autoecole_heures_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
