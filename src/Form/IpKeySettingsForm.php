<?php

namespace Drupal\member_login\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure ipdata API key settings for this site.
 */

class IpKeySettingsForm extends ConfigFormBase {
  
  /** @var string Config settings */
  const SETTINGS = 'ipApikey.settings';
   
  /** 
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'member_login_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);
     
    $ip_url = Url::fromUri('https://ipdata.co/',['attributes' => ['target' => '_blank']]);
    $ipdata_link = Link::fromTextAndUrl($this->t('ipdata'), $ip_url)->toString();
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ipdata API key'),
      '#default_value' => $config->get('api_key'),
      '#description' => 'Get ipdata api key from '. $ipdata_link,
    ];
  
    $form['markup'] = [
      '#markup' => $ipdata_link . ' is a fast, free, highly available IP Geolocation API with reliable performance.
       Sing up to get a free api key, otherwide default testing key will apply.',
    ];

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->configFactory->getEditable(static::SETTINGS)
    // Set the submitted configuration setting
    ->set('api_key', $form_state->getValue('api_key'))
    ->save();
  
    parent::submitForm($form, $form_state);
  }

}
