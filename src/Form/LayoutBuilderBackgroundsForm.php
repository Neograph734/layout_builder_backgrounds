<?php

namespace Drupal\layout_builder_backgrounds\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class LayoutBuilderBackgroundsForm extends FormBase {

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'layout_builder_backgrounds';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    /** @var \Drupal\layout_builder\Section $section */
    $section = $form_state->get('section');

    // Leave early if there is no valid section.
    if (!$section) {
      return $form;
    }

    $form['background'] = [
      '#type'  => 'details',
      '#title' => t('Background'),
      '#open' => TRUE,
      '#weight' => 95,
    ];
    $form['background']['color'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Color'),
      '#default_value' => $section->getThirdPartySetting('layout_builder_backgrounds', 'color', NULL),
      '#description' => t('A valid <a href="https://developer.mozilla.org/docs/Web/CSS/color_value">CSS color</a> (Ex: "#336699", "red", "rgba(0,0,0,.5)", etc.'),
    ];
    $form['background']['media'] = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['image'],
      '#title' => $this->t('Image'),
      '#default_value' => $section->getThirdPartySetting('layout_builder_backgrounds', 'media', NULL),
      '#description' => $this->t('Upload or select a background image.'),
    ];
    $form['background']['position'] = [
      '#type' => 'select',
      '#title' => $this->t('Background position'),
      '#options' => [
        'left top' => $this->t('left top'),
        'left center' => $this->t('left center'),
        'left bottom' => $this->t('left bottom'),
        'center top' => $this->t('center top'),
        'center center' => $this->t('center center'),
        'center bottom' => $this->t('center bottom'),
        'right top' => $this->t('right top'),
        'right center' => $this->t('right center'),
      ],
      '#default_value' => $section->getThirdPartySetting('layout_builder_backgrounds', 'position', 'center center'),
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\layout_builder\Section $section */
    $section = $form_state->get('section');

    $section->setThirdPartySetting('layout_builder_backgrounds', 'color', $form_state->getValue(['layout_builder_backgrounds', 'background', 'color']));
    $section->setThirdPartySetting('layout_builder_backgrounds', 'media', $form_state->getValue(['layout_builder_backgrounds', 'background', 'media']));
    $section->setThirdPartySetting('layout_builder_backgrounds', 'position', $form_state->getValue(['layout_builder_backgrounds', 'background', 'position']));
    
    // TODO unsetThirdPartySetting on empty form values.
  }

}
