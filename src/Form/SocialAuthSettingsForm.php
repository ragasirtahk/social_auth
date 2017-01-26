<?php

namespace Drupal\social_auth\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures Social Auth settings.
 */
class SocialAuthSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_auth_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'social_auth.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $social_auth_config = $this->config('social_auth.settings');

    $form['social_auth'] = array(
      '#type' => 'details',
      '#title' => $this->t('Social Auth Settings'),
      '#open' => TRUE,
      '#description' => $this->t('These settings allow you to configure how Social Auth module behaves on your Drupal site'),
    );

    $form['social_auth']['post_login_path'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Post login path'),
      '#description' => $this->t('Drupal path where the user should be redirected after successful login. Use <em>&lt;front&gt;</em> to redirect user to your front page. Leave it empty to set the path to page where the process started.'),
      '#default_value' => $social_auth_config->get('post_login_path'),
    );

    $form['social_auth']['redirect_user_form'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Redirect new users to Drupal user form'),
      '#description' => $this->t('If you check this, new users are redirected to Drupal user form after the user is created. This is useful if you want to encourage users to fill in additional user fields.'),
      '#default_value' => $social_auth_config->get('redirect_user_form'),
    );

    $form['social_auth']['disable_admin_login'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Disable Social Auth login for administrator'),
      '#description' => $this->t('Disabling Social Auth login for administrator (<em>user 1</em>) can help protect your site if a security vulnerability is ever discovered in some Social Network PHP SDK or this module.'),
      '#default_value' => $social_auth_config->get('disable_admin_login'),
    );

    // Option to disable Social Auth for specific roles.
    $roles = user_roles();
    $options = array();
    foreach ($roles as $key => $role_object) {
      if ($key != 'anonymous' && $key != 'authenticated') {
        $options[$key] = Html::escape($role_object->get('label'));
      }
    }

    $form['social_auth']['disabled_roles'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Disable Social Auth login for the following roles'),
      '#options' => $options,
      '#default_value' => $social_auth_config->get('disabled_roles'),
    );
    if (empty($roles)) {
      $form['social_auth']['disabled_roles']['#description'] = t('No roles found.');
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('social_auth.settings')
      ->set('post_login_path', $values['post_login_path'])
      ->set('redirect_user_form', $values['redirect_user_form'])
      ->set('disable_admin_login', $values['disable_admin_login'])
      ->set('disabled_roles', $values['disabled_roles'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}