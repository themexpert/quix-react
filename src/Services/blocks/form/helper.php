<?php

/**
 * @version    1.0.0
 * @package    Contact Form Quix element
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Crypt\Crypt;

/**
* QuixSimpleContactElement helper class
*/
class QuixFormElement
{
	
	public static function getAjax()
	{
		// Check for request forgeries.
		if( !JSession::checkToken('post') ){
			return new Exception("<div class='alert alert-danger'>" . JText::_('JINVALID_TOKEN') . "</div>");
		}

		$lang = JFactory::getLanguage();
		$extension = 'form';
		$base_dir = dirname(__FILE__);
		$language_tag = 'en-GB';
		$reload = true;
		$lang->load($extension, $base_dir, $language_tag, $reload);

		$app 	= JFactory::getApplication();
		$data   = $app->input->get('jform', array(), 'array');
		
		// $info = json_decode(base64_decode($data['info']));
		$info = QuixFormElement::getInfoFromEncrypted($data['info']);

		$afterSubmit = $info->general->form_action_after_submit;
		
		// form options
		if(isset($info->general->form_advanced)){
			$form_advanced = $info->general->form_advanced;
		}
		else
		{
			$form_advanced = $info->general->form_basic;
		}
		
		$options = [];
		foreach ($form_advanced as $key => $formAdvOptions) {
			$name = key($formAdvOptions);
			$options[$name] = $formAdvOptions->$name;
		}
		
		if($options['custom_message'] and !empty($options['message_success'])){
			$successMessage = $options['message_success'];
		}else{
			$successMessage = JText::_('COM_QUIX_FORM_SUBMIT_SUCCESSFUL');
		}
		
		if($options['custom_message'] and !empty($options['message_error'])){
			$errorMessage = $options['message_error'];
		}else{
			$errorMessage = JText::_('COM_QUIX_FORM_SUBMIT_ERROR');
		}

		if($options['custom_message'] and !empty($options['captcha_error'])){
			$captchaError = $options['captcha_error'];
		}else{
			$captchaError = JText::_('COM_QUIX_FORM_CAPTCHA_ERROR');
		}
		

		//validate captcha first
		$validate = QuixFormElement::validateCaptcha($data, $options);
		if(!$validate or $validate === false){
			return new Exception("<div class='alert alert-danger'>" . $captchaError . "</div>"); 
		}

		foreach ($afterSubmit as $key => $item) {
			$name = key($item);
			if($name == 'actions')
			{
				$actions = (array) $item->$name; break;
			}
		}

		//prepare data of fields
		$form_fields = $info->general->form_fields;
		
		$new_form_fields = [];// array(index => array( index => object name->name, object value->value ))
		foreach ($form_fields as $key => $form_field) {
			$new_form_fields[$key] = [];
			foreach ($form_field as $key2 => $field) {
				$fieldKey = key($field);

				$new_form_fields[$key][$key2] = new stdClass;
				$new_form_fields[$key][$key2]->name = $fieldKey;
				$new_form_fields[$key][$key2]->value = $field->$fieldKey;
			}
		}

		// validate form value
		$validateForm = QuixFormElement::validateSubmit($data, $new_form_fields);
		if(!$validateForm or $validateForm === false){
			return new Exception("<div class='alert alert-danger'>" . JText::_('COM_QUIX_FORM_NOT_VALID') . "</div>"); 
		}

		$info->general->form_fields = $new_form_fields;
		
		$results = array();		
		foreach ($actions as $key => $action) {
			$className = 'QuixFormElementHelper'.ucfirst($action);
			JLoader::register($className, __DIR__ . '/helpers/' . $action . '.php');

			$name = "form_" . $action;
			$config = $info->general->$name;

			$results[] = $className::action($data, $config, $info);
		}
		
		if(in_array(false, $results))
		{
			// get the error
			return new Exception("<div class='alert alert-warning'>" . $errorMessage . "</div>"); 
		}
		else
		{
			$_SESSION['quix_recaptcha'] = [
			    'first_number' => rand(1, 10),
			    'second_number' => rand(1, 10)
			];
        	// return success
			return ("<div class='alert alert-success'>" . $successMessage . "</div>");

		}
	}

	public static function validateCaptcha($data, $options)
	{
		$requiredRecaptcha = isset($options['required-recaptcha']) ? $options['required-recaptcha'] : false;
        $reCaptchaType = isset($options['recaptcha_type']) ? $options['recaptcha_type'] : 'math' ;

        // captcha validation....
        if ($requiredRecaptcha) {

            if ($reCaptchaType == 'recaptcha_invisible') {
                $config = JFactory::getConfig()->get('captcha');
                $captcha = JCaptcha::getInstance($config);
                $completed = $captcha->CheckAnswer($data['info']);

                if ($completed === false) {
                    return false;
                }
            } else {
                $firstNumber = $_SESSION['quix_recaptcha']['first_number'];
                $secondNumber = $_SESSION['quix_recaptcha']['second_number'];

                if (($firstNumber + $secondNumber) != $data['recaptcha_value']) {
                    return false;
                }
            }
        }

        return true;
	}

	public static function validateSubmit($data, $form_fiels)
	{
		$required = [];
        foreach ($form_fiels as $key => $fields) {
            $name = strtolower($fields[0]->value);
            $value = (isset($data[$name]) ? $data[$name] : '');

            foreach ($fields as $key2 => $field) {
                if ($field->name == 'required') {
                    if (!empty($field->value)) {
                        $required[$name] = $field->value;
                    }
                }
            }
        }
        $valid = true;
        if($required){
        	foreach ($required as $key=>$item) {
        		if(!isset($data[$key]) or empty($data[$key])){
        			 $valid = false;
        			 break;
        		}
        	}
        }

        return $valid;
	}

	public static function getInfoFromEncrypted($string){
		$configSys = JFactory::getConfig();
        $session = JFactory::getSession();
        if($session->get('quix_form_secret')){
            $key = $session->get('quix_form_secret');
        }else{
            $secret = $configSys->get('secret');
            $encCrypt = new Crypt(null, null);
            $key = $encCrypt->generateKey();
            $session->set('quix_form_secret', $key);
        }
        $enc = new Crypt(null, $key);

        $decrypt = $enc->decrypt($string);
        return json_decode($decrypt);
	}

}
