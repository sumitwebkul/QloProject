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

class AdminAddHotelController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_branch_info';
        $this->className = 'HotelBranchInformation';
        $this->identifier = 'id';
        parent::__construct();

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
        ON (a.id = hbl.id AND hbl.`id_lang` = '.(int) $this->context->language->id.')';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`state_id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl
        ON (cl.`id_country` = a.`country_id` AND cl.`id_lang` = '.(int) $this->context->language->id.')';

        $this->_select = ' hbl.`hotel_name`, s.`name` as `state_name`, cl.`name` as country_name';

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'hotel_name' => array(
                'title' => $this->l('Hotel Name'),
                'align' => 'center',
            ),
            'city' => array(
                'title' => $this->l('City'),
                'align' => 'center',
            ),
            'state_name' => array(
                'title' => $this->l('State'),
                'align' => 'center',
                'filter_key' => 's!name',
            ),
            'country_name' => array(
                'title' => $this->l('Country'),
                'align' => 'center',
                'filter_key' => 'cl!name',
            ),
            'active' => array(
                'align' => 'center',
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new Hotel'),
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $smartyVars = array();
        //tinymce setup
        $smartyVars['path_css'] = _THEME_CSS_DIR_;
        $smartyVars['ad'] = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);
        $smartyVars['autoload_rte'] = true;
        $smartyVars['lang'] = true;
        $smartyVars['iso'] = $this->context->language->iso_code;
        //lang vars
        $currentLangId = Configuration::get('PS_LANG_DEFAULT');
        $smartyVars['languages'] = Language::getLanguages(false);
        $smartyVars['currentLang'] = Language::getLanguage((int) $currentLangId);

        $countries = Country::getCountries($this->context->language->id);
        $smartyVars['country_var'] = $countries;

        $country = $this->context->country;
        $smartyVars['defaultCountry'] = $country->name[Configuration::get('PS_LANG_DEFAULT')];

        if ($this->display == 'edit') {
            $idHotel = Tools::getValue('id');
            $objHtlBranch = new HotelBranchInformation();
            $hotel_branch_info = $objHtlBranch->hotelBranchInfoById($idHotel);

            $statesbycountry = State::getStatesByIdCountry($hotel_branch_info['country_id']);

            $states = array();
            if ($statesbycountry) {
                foreach ($statesbycountry as $key => $value) {
                    $states[$key]['id'] = $value['id_state'];
                    $states[$key]['name'] = $value['name'];
                }
            }
            $smartyVars['edit'] =  1;
            $smartyVars['country_var'] =  $countries;
            $smartyVars['state_var'] =  $states;
            $smartyVars['hotel_info'] =  $hotel_branch_info;
            //Hotel Images
            $objHotelImage = new HotelImage();
            $hotelAllImages = $objHotelImage->getAllImagesByHotelId($idHotel);
            if ($hotelAllImages) {
                foreach ($hotelAllImages as &$image) {
                    $image['image_link'] = _MODULE_DIR_.$this->module->name.'/views/img/hotel_img/'.
                    $image['hotel_image_id'].'.jpg';
                }
                $smartyVars['hotelImages'] =  $hotelAllImages;
            }
        }
        $smartyVars['enabledDisplayMap'] =  Configuration::get('WK_GOOGLE_ACTIVE_MAP');
        $smartyVars['ps_img_dir'] = _PS_IMG_.'l/';
        $this->context->smarty->assign($smartyVars);
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        return parent::renderForm();
    }

    public function processSave()
    {
        $idHotel = Tools::getValue('id');
        $hotel_name = Tools::getValue('hotel_name');
        $phone = Tools::getValue('phone');
        $email = Tools::getValue('email');
        $check_in = Tools::getValue('check_in');
        $check_out = Tools::getValue('check_out');
        $short_description = Tools::getValue('short_description');
        $description = Tools::getValue('description');
        $rating = Tools::getValue('hotel_rating');
        $city = Tools::getValue('hotel_city');
        $state = Tools::getValue('hotel_state');
        $country = Tools::getValue('hotel_country');
        $policies = Tools::getValue('hotel_policies');
        $zipcode = Tools::getValue('hotel_postal_code');
        $address = Tools::getValue('address');
        $active = Tools::getValue('ENABLE_HOTEL');
        $latitude = Tools::getValue('loclatitude');
        $longitude = Tools::getValue('loclongitude');
        $map_formated_address = Tools::getValue('locformatedAddr');
        $map_input_text = Tools::getValue('googleInputField');

        if ($hotel_name == '') {
            $this->errors[] = $this->l('Hotel name is required field.');
        } elseif (!Validate::isGenericName($hotel_name)) {
            $this->errors[] = $this->l('Hotel name must not have Invalid characters <>;=#{}');
        }

        if (!$phone) {
            $this->errors[] = $this->l('Phone number is required field.');
        } elseif (!Validate::isPhoneNumber($phone)) {
            $this->errors[] = $this->l('Please enter a valid phone number.');
        }

        if ($email == '') {
            $this->errors[] = $this->l('Email is required field.');
        } elseif (!Validate::isEmail($email)) {
            $this->errors[] = $this->l('Please enter a valid email.');
        }

        if ($check_in == '') {
            $this->errors[] = $this->l('Check In time is required field.');
        }

        if ($check_out == '') {
            $this->errors[] = $this->l('Check Out Time is required field.');
        }

        if ($zipcode == '') {
            $this->errors[] = $this->l('Postal Code is required field.');
        } elseif (!Validate::isPostCode($zipcode)) {
            $this->errors[] = $this->l('Enter a Valid Postal Code.');
        }

        if (!$rating) {
            $this->errors[] = $this->l('Rating is required field.');
        }

        if ($address == '') {
            $this->errors[] = $this->l('Address is required field.');
        }

        if (!$country) {
            $this->errors[] = $this->l('Country is required field.');
        } else {
            $statesbycountry = State::getStatesByIdCountry($country);
            /*If selected country has states only the validate state field*/

            if (!$state) {
                if ($statesbycountry) {
                    $this->errors[] = $this->l('State is required field.');
                }
            }
        }

        if ($city == '') {
            $this->errors[] = $this->l('City is required field.');
        } elseif (!Validate::isCityName($city)) {
            $this->errors[] = $this->l('Enter a Valid City Name.');
        }

        if (!count($this->errors)) {
            if ($idHotel) {
                $obj_hotel_info = new HotelBranchInformation($idHotel);
            } else {
                $obj_hotel_info = new HotelBranchInformation();
            }

            if ($obj_hotel_info->id) {
                if (!$active) {
                    $obj_htl_rm_info = new HotelRoomType();
                    $ids_product = $obj_htl_rm_info->getIdProductByHotelId($obj_hotel_info->id);
                    if (isset($ids_product) && $ids_product) {
                        foreach ($ids_product as $key_prod => $value_prod) {
                            $obj_product = new Product($value_prod['id_product']);
                            if ($obj_product->active) {
                                $obj_product->toggleStatus();
                            }
                        }
                    }
                }
            }

            $obj_hotel_info->active = $active;
            $obj_hotel_info->hotel_name = $hotel_name;
            $obj_hotel_info->phone = $phone;
            $obj_hotel_info->email = $email;
            $obj_hotel_info->check_in = $check_in;
            $obj_hotel_info->check_out = $check_out;
            $obj_hotel_info->short_description = $short_description;
            $obj_hotel_info->description = $description;
            $obj_hotel_info->rating = $rating;
            $obj_hotel_info->city = $city;
            $obj_hotel_info->state_id = $state;
            $obj_hotel_info->country_id = $country;
            $obj_hotel_info->zipcode = $zipcode;
            $obj_hotel_info->policies = $policies;
            $obj_hotel_info->address = $address;
            $obj_hotel_info->latitude = $latitude;
            $obj_hotel_info->longitude = $longitude;
            $obj_hotel_info->map_formated_address = $map_formated_address;
            $obj_hotel_info->map_input_text = $map_input_text;
            $obj_hotel_info->save();

            $new_hotel_id = $obj_hotel_info->id;
            if ($new_hotel_id) {
                $grp_ids = array();
                $data_grp_ids = Group::getGroups($this->context->language->id);

                foreach ($data_grp_ids as $key => $value) {
                    $grp_ids[] = $value['id_group'];
                }
                //test
                $country_name = (new Country())->getNameById($this->context->language->id, $country);
                $cat_country = $obj_hotel_info->addCategory($country_name, false, $grp_ids);

                if ($cat_country) {
                    if ($state) {
                        $state_name = (new State())->getNameById($state);
                        $cat_state = $obj_hotel_info->addCategory($state_name, $cat_country, $grp_ids);
                    } else {
                        $cat_state = $obj_hotel_info->addCategory($city, $cat_country, $grp_ids);
                    }
                }
                if ($cat_state) {
                    $cat_city = $obj_hotel_info->addCategory($city, $cat_state, $grp_ids);
                }

                if ($cat_city) {
                    $cat_hotel = $obj_hotel_info->addCategory($hotel_name, $cat_city, $grp_ids, 1, $new_hotel_id);
                }

                if ($cat_hotel) {
                    $obj_hotel_info = new HotelBranchInformation($new_hotel_id);
                    $obj_hotel_info->id_category = $cat_hotel;
                    $obj_hotel_info->save();
                }
            }

            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                if ($idHotel) {
                    Tools::redirectAdmin(
                        self::$currentIndex.'&id='.(int) $new_hotel_id.'&update'.$this->table.'&conf=4&token='.
                        $this->token
                    );
                } else {
                    Tools::redirectAdmin(
                        self::$currentIndex.'&id='.(int) $new_hotel_id.'&update'.$this->table.'&conf=3&token='.
                        $this->token
                    );
                }
            } else {
                if ($idHotel) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                }
            }
        } else {
            if ($idHotel) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function ajaxProcessStateByCountryId()
    {
        $country_id = Tools::getValue('id_country');
        $states = array();
        $statesbycountry = State::getStatesByIdCountry($country_id);
        if ($statesbycountry) {
            $states = array();
            foreach ($statesbycountry as $key => $value) {
                $states[$key]['id'] = $value['id_state'];
                $states[$key]['name'] = $value['name'];
            }
            if (isset($states)) {
                die(Tools::jsonEncode($states));
            } else {
                die(Tools::jsonEncode($states));
            }
        } else {
            die(Tools::jsonEncode($states));
        }
    }

    public function ajaxProcessUploadHotelImages()
    {
        $idHotel = Tools::getValue('id_hotel');
        if ($idHotel) {
            $invalidImg = ImageManager::validateUpload(
                $_FILES['hotel_image'],
                Tools::getMaxUploadSize(Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE') * 1048576)
            );
            if (!$invalidImg) {
                // Add Hotel images
                $kwargs = [
                    'id_hotel' => $idHotel,
                    'hotel_image' => $_FILES['hotel_image'],
                ];
                $objHotelImage = new HotelImage();
                $hotelImgPath = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/';
                $imageDetail = $objHotelImage->uploadHotelImages($_FILES['hotel_image'], $idHotel, $hotelImgPath);
                if ($imageDetail) {
                    die(Tools::jsonEncode($imageDetail));
                } else {
                    die(Tools::jsonEncode(array('hasError' => true)));
                }
            } else {
                die(Tools::jsonEncode(array('hasError' => true, 'message' => $_FILES['hotel_image']['name'].': '.$invalidImg)));
            }
        } else {
            die(Tools::jsonEncode(array('hasError' => true)));
        }
    }

    public function ajaxProcessChangeCoverImage()
    {
        $idImage = Tools::getValue('id_image');
        if ($idImage) {
            $idHotel = Tools::getValue('id_hotel');
            if ($coverImg = HotelImage::getCover($idHotel)) {
                $objHtlImage = new HotelImage((int) $coverImg['id']);
                $objHtlImage->cover = 0;
                $objHtlImage->save();
            }
            $objHtlImage = new HotelImage((int) $idImage);
            $objHtlImage->cover = 1;
            if ($objHtlImage->update()) {
                die(true);
            } else {
                die(false);
            }
        } else {
            die(false);
        }
    }

    public function ajaxProcessDeleteHotelImage()
    {
        $idImage = Tools::getValue('id_image');
        if ($idImage) {
            $idHotel = Tools::getValue('id_hotel');
            $objHtlImage = new HotelImage((int) $idImage);
            if ($objHtlImage->delete()) {
                if (!HotelImage::getCover($idHotel)) {
                    $images = $objHtlImage->getAllImagesByHotelId($idHotel);
                    if ($images) {
                        $objHtlImage = new HotelImage($images[0]['id']);
                        $objHtlImage->cover = 1;
                        $objHtlImage->save();
                    }
                }

                if (file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_img/'.$idImage.'.jpg')) {
                    @unlink(_PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_img/'.$idImage.'.jpg');
                }
                die(true);
            } else {
                die(false);
            }
        } else {
            die(false);
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        // GOOGLE MAP
        $language = $this->context->language;
        $country = $this->context->country;
        $WK_GOOGLE_API_KEY = Configuration::get('WK_GOOGLE_API_KEY');
        $this->addJs("https://maps.googleapis.com/maps/api/js?key=$WK_GOOGLE_API_KEY&libraries=places&language=$language->iso_code&region=$country->iso_code");

        //tinymce
        $this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');

        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_.'tinymce.inc.js');
        }
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/hotelImage.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/HotelReservationAdmin.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/HotelReservationAdmin.css');
    }
}
