<?php
/**
* 2007-2025 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2025 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product;

class Itrmanagecontent extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'itrmanagecontent';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'IT ROOM ';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('itrmanagecontent');
        $this->description = $this->l('This module is made to manage front content, admin banners and report product issues.');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('ITRMANAGECONTENT_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('displayCustomerAccount') &&
            $this->registerHook('displayNav') &&
            $this->registerHook('displayProductButtons') &&
            $this->registerHook('displayProductExtraContent') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayTop');

    }

    public function uninstall()
    {
        Configuration::deleteByName('ITRMANAGECONTENT_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitItrmanagecontentModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = '';

        $output .= $this->renderStats(); 
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        $output .= $this->renderForm();

        return $output;
  
    }

    protected function renderStats()
    {
        $sql_customers = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'customer WHERE active = 1';
        $sql_orders = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'orders WHERE valid = 1';
        $sql_total_sales = 'SELECT SUM(total_paid) FROM '._DB_PREFIX_.'orders WHERE valid = 1';

        $nb_customers = Db::getInstance()->getValue($sql_customers);
        $nb_orders = Db::getInstance()->getValue($sql_orders);
        $total_sales = Db::getInstance()->getValue($sql_total_sales);
        $total_sales = $total_sales ?? 0;

        $priceFormatter = new PriceFormatter();
        $formattedTotalSales = $priceFormatter->format($total_sales, (int)Configuration::get('PS_CURRENCY_DEFAULT'));

        return '
        <div class="panel">
            <h3><i class="icon-bar-chart"></i> Statistiques générales</h3>
            <ul>
                <li><strong>Clients actifs :</strong> '.$nb_customers.'</li>
                <li><strong>Commandes validées :</strong> '.$nb_orders.'</li>
                <li><strong>Total des ventes :</strong> '.$formattedTotalSales.'</li>
            </ul>
        </div>';
    }





    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitItrmanagecontentModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Paramètres des blocs HTML'),
                    'icon' => 'icon-html5',
                ),
                'input' => array(
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Bloc HTML (utilisateur NON connecté)'),
                        'name' => 'ITR_HTML_GUEST',
                        'autoload_rte' => true, // Active l'éditeur WYSIWYG
                        'desc' => $this->l('Contenu HTML à afficher aux visiteurs non connectés.'),
                        'lang' => false,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Bloc HTML (utilisateur CONNECTÉ)'),
                        'name' => 'ITR_HTML_LOGGED',
                        'autoload_rte' => true,
                        'desc' => $this->l('Contenu HTML à afficher aux utilisateurs connectés.'),
                        'lang' => false,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Enregistrer'),
                ),
            ),
        );
    }


    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'ITR_HTML_GUEST' => Configuration::get('ITR_HTML_GUEST'),
            'ITR_HTML_LOGGED' => Configuration::get('ITR_HTML_LOGGED'),
        );
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookActionAdminControllerSetMedia()
    {
        /* Place your code here. */
    }

    public function hookDisplayCustomerAccount()
    {
        /* Place your code here. */
    }

    public function hookDisplayNav()
    {
        /* Place your code here. */
    }

    public function hookDisplayProductButtons()
    {
        /* Place your code here. */
    }

    public function hookDisplayProductExtraContent()
    {
        /* Place your code here. */
    }

    public function hookDisplayHome()
    {
        $isLogged = $this->context->customer->isLogged();
        $content = $isLogged
            ? Configuration::get('ITR_HTML_LOGGED')
            : Configuration::get('ITR_HTML_GUEST');

        return $content;
    }

    public function hookDisplayTop($params)
    {
        $context  = Context::getContext();
        $customer = $context->customer;

        // On  laisse que les clients loggés du groupe "Administrateur Front"
        if (!$customer->isLogged()) {
            return '';
        }
        $allowed = false;
        foreach ($customer->getGroups() as $id_group) {
            $group = new Group($id_group);
            if (isset($group->name[$context->language->id])
                && $group->name[$context->language->id] === 'Administrateur Front'
            ) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            return '';
        }

        //Nombre de produits actifs
        $nb_products = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'product` WHERE active = 1'
        );

        
        $avg_cart = Db::getInstance()->getValue(
            'SELECT AVG(total_paid) FROM `' . _DB_PREFIX_ . 'orders` WHERE valid = 1'
        ) ?: 0;
        $formatted_avg_cart = $context->currentLocale->formatPrice(
            (float) $avg_cart,
            $context->currency->iso_code
        );

        // 4) Produit le plus commandé via la méthode cœur (PAS de SQL à la main)
        $bestSales = ProductSaleCore::getBestSales((int)$this->context->language->id, 0, 1);
        if (!empty($bestSales)) {
            $best = $bestSales[0];
            $product_name = $best['name'];
            $product_link = $this->context->link->getProductLink((int)$best['id_product']);
        } else {
            $product_name = 'Aucun produit';
            $product_link = '#';
        }

        // 5) On alimente le template
        $this->context->smarty->assign([
            'nb_products'  => $nb_products,
            'avg_cart'     => $formatted_avg_cart,
            'product_name' => $product_name,
            'product_link' => $product_link,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/admin_front_banner.tpl');
    }

}
