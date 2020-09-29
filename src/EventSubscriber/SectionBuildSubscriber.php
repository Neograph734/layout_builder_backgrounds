<?php

namespace Drupal\layout_builder_backgrounds\EventSubscriber;

use Drupal\file\Entity\File;
use Drupal\layout_builder\Event\SectionBuildRenderArrayEvent ;
use Drupal\layout_builder\LayoutBuilderEvents;
use Drupal\media\Entity\Media;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SectionBuildSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      LayoutBuilderEvents::SECTION_BUILD_RENDER_ARRAY => 'onBuildRender',
    ];
  }

  /**
   * Builds render arrays for block plugins and sets it on the event.
   *
   * @param \Drupal\layout_builder\Event\SectionBuildRenderArrayEvent  $event
   *   The section component render event.
   */
  public function onBuildRender(SectionBuildRenderArrayEvent  $event) {
    $third_party_settings = $event->getSection()->getThirdPartySettings('layout_builder_backgrounds');
    $build = $event->getBuild();

    // Apply a background to a layout by adding inline CSS if one is set.
    if (!empty($third_party_settings)) {

      $color = $third_party_settings['color'] ?? NULL;
      $media = $third_party_settings['media'] ?? NULL;
      $position = $third_party_settings['position'] ?? NULL;

      // Add a generic class to indicate a background is specified.
      $new_classes = ['layout-builder-backgrounds'];
      $new_styles = [];

      if ($color) {
        $new_styles[] = 'background-color: ' . $color . ';';
      }

      if ($media) {
        $media_entity = Media::load($media);
        $fid = $media_entity->getSource()->getSourceFieldValue($media_entity);
        $file = File::load($fid);
        $url = $file->url();
        $media_image_styles = [
          'background-image: url(' . $url . ');',
          'background-position: ' . $position . ';',
          'background-size: cover;',
          'background-repeat: no-repeat;',
        ];
        $new_styles = array_merge($new_styles, $media_image_styles);
      }

      // Update class attribute.
      if (!isset($build['#attributes']['class']) || !is_array($build['#attributes']['class'])) {
        $build['#attributes']['class'] = [];
      }
      $build['#attributes']['class'] = array_merge($build['#attributes']['class'], $new_classes);

      // Update style attribute.
      if (!isset($build['#attributes']['style']) || !is_array($build['#attributes']['style'])) {
        $build['#attributes']['style'] = [];
      }
      $build['#attributes']['style'] = array_merge($build['#attributes']['style'], $new_styles);
    }

    $event->setBuild($build);
  }

}
