<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../wktestimonialblock/classes/WkHotelTestimonialData.php';
require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';

class WkTestimonialBlock extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'wktestimonialblock';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Hotel Testimonial');
        $this->description = $this->l('Show Hotel testimonials on home page using this module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookDisplayAddModuleSettingLink()
    {
        $href_testimonials_conf = $this->context->link->getAdminLink('AdminTestimonialsModuleSetting');
        $this->context->smarty->assign('testimonials_setting_link', $href_testimonials_conf);
        return $this->display(__FILE__, 'hotelTestimonialSettingLink.tpl');
    }

    public function hookDisplayHome()
    {
        // These files are already included in "wkabouthotelblock" module
        if (!(Module::isInstalled('wkabouthotelblock') && Module::isEnabled('wkabouthotelblock'))) {
            // owl.carousel Plug-in files
            $this->context->controller->addCSS(
                _PS_MODULE_DIR_.'hotelreservationsystem/libs/owl.carousel/assets/owl.carousel.min.css'
            );
            $this->context->controller->addCSS(
                _PS_MODULE_DIR_.'hotelreservationsystem/libs/owl.carousel/assets/owl.theme.default.min.css'
            );
            $this->context->controller->addJS(
                _PS_MODULE_DIR_.'hotelreservationsystem/libs/owl.carousel/owl.carousel.min.js'
            );
        }
        /*---- Module Files ----*/
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/WkTestimonialBlockFront.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/WkTestimonialBlockFront.js');

        $HOTEL_TESIMONIAL_BLOCK_HEADING = Configuration::get('HOTEL_TESIMONIAL_BLOCK_HEADING');
        $HOTEL_TESIMONIAL_BLOCK_CONTENT = Configuration::get('HOTEL_TESIMONIAL_BLOCK_CONTENT');

        $objTestimonialData = new WkHotelTestimonialData();
        $testimonials_data = $objTestimonialData->getTestimonialData(1);
        $this->context->smarty->assign(
            array(
                'HOTEL_TESIMONIAL_BLOCK_HEADING' => $HOTEL_TESIMONIAL_BLOCK_HEADING,
                'HOTEL_TESIMONIAL_BLOCK_CONTENT' => $HOTEL_TESIMONIAL_BLOCK_CONTENT,
                'testimonials_data' => $testimonials_data,
            )
        );
        return $this->display(__FILE__, 'wktestimonialblock.tpl');
    }

    public function hookDisplayDefaultNavigationHook()
    {
        return $this->display(__FILE__, 'hotelTestimonialNaviagtionMenu.tpl');
    }

    public function hookDisplayFooterExploreSectionHook()
    {
        return $this->display(__FILE__, 'hotelTestimonialFooterExploreLink.tpl');
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        }
        $sql = str_replace(array('PREFIX_',  'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }
        if (!parent::install()
            || !$this->registerHook('displayHome')
            || !$this->registerHook('displayFooterExploreSectionHook')
            || !$this->registerHook('displayAddModuleSettingLink')
            || !$this->registerHook('displayDefaultNavigationHook')
            || !$this->callInstallTab()
            || !$this->insertDefaultHotelTestimonialsEntries()
        ) {
            return false;
        }
        return true;
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those controllers...
        $this->installTab('AdminTestimonialsModuleSetting', 'testimonial configutaion');
        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name=false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }
        if ($tab_parent_name) {
            $tab->id_parent = (int)Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = -1;
        }
        $tab->module = $this->name;
        $res = $tab->add();
        //Set position of the Hotel reservation System Tab to the position where we want...
        return $res;
    }

    public function deleteConfigKeys()
    {
        $var = array(
            'HOTEL_TESIMONIAL_BLOCK_HEADING',
            'HOTEL_TESIMONIAL_BLOCK_CONTENT'
        );
        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }
        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_testimonials_block_data`'
        );
    }


    public function callUninstallTab()
    {
        $this->uninstallTab('AdminTestimonialsModuleSetting');
        return true;
    }

    public function uninstallTab($class_name)
    {
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        } else {
            return false;
        }
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()
            || !$this->deleteTables()
            || !$this->callUninstallTab()
            || !$this->deleteConfigKeys()
            || !$this->deleteTestimonialUserImage()
        ) {
            return false;
        }
        return true;
    }

    public function deleteTestimonialUserImage()
    {
        $uploadedImg = glob(_PS_MODULE_DIR_.$this->name.'/views/img/hotels_testimonials_img/*.jpg');
        if ($uploadedImg) {
            foreach ($uploadedImg as $interiorImg) {
                unlink($interiorImg);
            }
        }
        return true;
    }

    public function insertDefaultHotelTestimonialsEntries()
    {
        $HOTEL_TESIMONIAL_BLOCK_HEADING = $this->l('What our Guest say?');
        $HOTEL_TESIMONIAL_BLOCK_CONTENT = $this->l('Fap put a bird on it next level, sustainable disrupt polaroid
        flannel Helvetica Kickstarter quinoa bicycle rights narwhal wolf Fap put a bird on it next level.');

        Configuration::updateValue('HOTEL_TESIMONIAL_BLOCK_HEADING', $HOTEL_TESIMONIAL_BLOCK_HEADING);
        Configuration::updateValue('HOTEL_TESIMONIAL_BLOCK_CONTENT', $HOTEL_TESIMONIAL_BLOCK_CONTENT);

        $designations = array(0 => 'Eon Comics CEO', 1 => 'Ken Comics Kal', 2 => 'Jan Comics Joe');
        $images = array(0 => '1.png', 1 => '2.png', 2 => '3.png');
        $names = array(0 => 'Calrk Kent', 1 => 'Calrk Kent', 2 => 'Calrk Kent');

        $testimonialContent = $this->l("It is a long established fact that a reader will be distracted by the readable
        content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less
        normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable
        English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text,
        and a search for 'lorem ipsum' will uncover many web sites still in their infancy.");

        for ($i = 0; $i < 3; $i++) {
            $objTestimonialData = new WkHotelTestimonialData();
            $objTestimonialData->name = $names[$i];
            $objTestimonialData->designation = $designations[$i];
            $objTestimonialData->testimonial_content = $testimonialContent;

            ImageManager::resize(
                _PS_MODULE_DIR_.$this->name.'/views/img/dummy_img/'.($i+1).'.png',
                _PS_MODULE_DIR_.$this->name.'/views/img/hotels_testimonials_img/'.($i+1).'.jpg'
            );

            $objTestimonialData->position = WkHotelTestimonialData::getHigherPosition();
            $objTestimonialData->testimonial_image = ($i+1).'.jpg';
            $objTestimonialData->active = 1;
            $objTestimonialData->save();
        }
        return true;
    }
}
