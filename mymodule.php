<?php

/*
 *  @author d@rkWolf <darkWolf.PR@seznam.cz>
 *  @copyright  2016 d@rkWolf
 */

if (!defined('_PS_VERSION_'))
    exit;

class MyModule extends Module
{

    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'd@rkWolf';
        $this->need_instance = 0;
        $this->ps_version_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('My module');
        $this->description = $this->l('description');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !Configuration::updateValue('MYMODULE_NAME', 'test value')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME')
        ) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $myModuleName = strval(Tools::getValue('MYMODULE_NAME'));
            if (!$myModuleName
                || empty($myModuleName)
                || !Validate::isGenericName($myModuleName)
            ) {
                $output .= $this->displayError($this->l('Invalid Configuration Value'));
            } else {
                Configuration::updateValue('MYMODULE_NAME', $myModuleName);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        //Get default language
        $defaultLang = (int)Configuration::get('PS_LAND_DEFAULT');

        //Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Configuration value'),
                    'name' => 'MYMODULE_NAME',
                    'size' => 20,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();

        //Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        //Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        //Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;   // false->remove toolbar
        $helper->toolbar_scroll = true; // yes->toolbar is always visible on top of the screen
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name.
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list'),
            )
        );

        //Load current value
        $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');

        return $helper->generateForm($fields_form);
    }

}
