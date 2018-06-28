<?php

use EventEspresso\PayPalSmartButtons\payment_methods\Paypal_Smart_Buttons\forms\PayPalSmartButtonBillingForm;
use EventEspresso\PayPalSmartButtons\payment_methods\Paypal_Smart_Buttons\forms\PayPalSmartButtonSettingsForm;

/**
 * EE_PMT_Paypal_Smart_Buttons
 *
 * @package         Event Espresso
 * @subpackage
 * @author              Mike Nelson
 *
 * ------------------------------------------------------------------------
 */
class EE_PMT_Paypal_Smart_Buttons extends EE_PMT_Base
{

    /**
     * @param EE_Payment_Method $pm_instance
     * @return EE_PMT_Paypal_Smart_Buttons
     */
    public function __construct($pm_instance = null)
    {
        $this->_default_button_url = $this->file_url() . 'lib/paypal-logo.png';
        require_once($this->file_folder().'EEG_Paypal_Smart_Buttons.gateway.php');
        $this->_gateway = new EEG_Paypal_Smart_Buttons();
        $this->_pretty_name = __("PayPal Express Checkout with Smart Buttons", 'event_espresso');
        $this->_default_description = __('Please select one of the following options provided by PayPal:', 'event_espresso');
        $this->_requires_https = false;
        parent::__construct($pm_instance);
    }



    /**
     * Gets the form for all the settings related to this payment method type
     * @return EE_Payment_Method_Form
     */
    public function generate_new_settings_form()
    {
        $form =  new PayPalSmartButtonSettingsForm(
            $this->get_help_tab_link()
        );
        return $form;
    }


    /**
     * Creates the billing form for this payment method type
     *
     * @param EE_Transaction $transaction
     * @return PayPalSmartButtonBillingForm
     */
    public function generate_new_billing_form(EE_Transaction $transaction = null)
    {
        return new PayPalSmartButtonBillingForm($this->_pm_instance, $transaction);
    }


    /**
     * Adds the help tab
     * @see EE_PMT_Base::help_tabs_config()
     * @return array
     */
    public function help_tabs_config()
    {
        return array(
            $this->get_help_tab_name() => array(
                        'title' => __('PayPal Express Checkout with Smart Buttons Settings', 'event_espresso'),
                        'filename' => 'payment_methods_overview_paypal_smart_buttons'
                        ),
        );
    }


    /**
     * @param EE_Transaction $transaction
     * @param null           $amount
     * @param null           $billing_info
     * @param null           $return_url
     * @param string         $fail_url
     * @param string         $method
     * @param bool           $by_admin
     * @return EE_Payment
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws \EventEspresso\core\exceptions\InvalidDataTypeException
     * @throws \EventEspresso\core\exceptions\InvalidInterfaceException
     */
    public function process_payment(
        EE_Transaction $transaction,
        $amount = null,
        $billing_info = null,
        $return_url = null,
        $fail_url = '',
        $method = 'CART',
        $by_admin = false
    ) {
        $result = parent::process_payment(
            $transaction,
            $amount,
            $billing_info,
            $return_url,
            $fail_url,
            $method,
            $by_admin
        ); // TODO: Change the autogenerated stub
        // check a new acccess token wasn't acquired in the process of processing the payment,
        // in which case we should update our stored access token
        $gateway = $this->get_gateway();
        /**
         * @var $gateway EEG_Paypal_Smart_Buttons
         */
        if ($gateway->accessTokenWasExpired()) {
            $this->_pm_instance->update_extra_meta(
                'access_token',
                $gateway->getLatestAccessToken()
            );
        }
        return $result;
    }
}
