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

class AdminHotelFeaturesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_branch_features';
        $this->className = 'HotelBranchFeatures';
        $this->identifier  = 'id';
        parent::__construct();
        $this->display = 'view';

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_branch_info` hi ON (hi.`id` = a.`id_hotel`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hil
        ON (hi.`id` = hil.`id` AND hil.id_lang = '.(int)$this->context->language->id.')';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = hi.`state_id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = hi.`country_id`
        AND cl.`id_lang` = '.(int)$this->context->language->id.')';

        $this->_select .= 'hi.`city` as htl_city, hi.`id` as htl_id, hil.`hotel_name` as htl_name,
        s.`name` as `state_name`, cl.`name`';
        $this->_group = 'GROUP BY a.`id_hotel`';

        $this->fields_list = array(
            'htl_id' => array(
                'title' => $this->l('Hotel ID'),
                'align' => 'center',
                'filter_key' => 'hi!id',
            ),
            'htl_name' => array(
                'title' => $this->l('Hotel Name'),
                'align' => 'center',
                'filter_key' => 'hi!hotel_name',
            ),
            'htl_city' => array(
                'title' => $this->l('City'),
                'align' => 'center',
                'filter_key' => 'hi!city',
            ),
            'state_name' => array(
                'title' => $this->l('State'),
                'align' => 'center',
                'filter_key' => 's!name',
            ),
            'name' => array(
                'title' => $this->l('Country'),
                'align' => 'center',
                'filter_key' => 'cl!name',
            ),
            'date_add' => array(
                'title' => $this->l('Date Added'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            )
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['addfeatures'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'&addfeatures=1',
            'desc' => $this->l('Add new Features'),
            'imgclass' => 'new'
        );
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Assign Features'),
        );
    }

    public function renderList()
    {
        unset($this->toolbar_btn['new']);

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if ($object->id) {
                $objBranchFeatures = new HotelBranchFeatures();
                $objBranchFeatures->deleteBranchFeaturesByHotelId($object->id_hotel);
            }
        } else {
            $this->errors[] = $this->l('An error occurred while deleting the object.').
                ' <b>'.$this->table.'</b> '.$this->l('(cannot load object)');
        }
        parent::processDelete();
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $key => $value) {
                $objBranchFeatures = new HotelBranchFeatures($value);
                $objBranchFeatures->deleteBranchFeaturesByHotelId($objBranchFeatures->id_hotel);
            }
            parent::processBulkDelete();
        } else {
            $this->errors[] = $this->l('You must select at least one element to delete.');
        }
    }

    public function renderView()
    {
        $objHotelFeatures = new HotelFeatures();
        $featuresList = $objHotelFeatures->HotelAllCommonFeaturesArray();
        $this->context->smarty->assign('features_list', $featuresList);
        return parent::renderView();
    }

    public function renderForm()
    {
        $smartyVars = array();
        //lang vars
        $currentLangId = Configuration::get('PS_LANG_DEFAULT');
        $smartyVars['languages'] = Language::getLanguages(false);
        $smartyVars['currentLang'] = Language::getLanguage((int) $currentLangId);
        $smartyVars['ps_img_dir'] = _PS_IMG_.'l/';
        $this->context->smarty->assign($smartyVars);

        Media::addJsDef(
            array(
                'img_dir_l' => _PS_IMG_.'l/',
            )
        );
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save')
            )
        );
        return parent::renderForm();
    }

    public function processSave()
    {
        if (Tools::getValue('addfeatures')) {
            //Process of assignig features
        } else {
            $editHtlId = Tools::getValue('edit_hotel_id');
            $objBranchFeatures = new HotelBranchFeatures();
            if ($editHtlId) {
                $objBranchFeatures->deleteBranchFeaturesByHotelId($editHtlId);
            }
            if ($idHotel = Tools::getValue('id_hotel')) {
                $hotelFeatures = Tools::getValue('hotel_fac');
                if (!$objHotelFeatures->assignFeaturesToHotel($idHotel, $hotelFeatures)) {
                    $this->errors[] = $this->l('Some error occured while assigning features to the hotel.');
                }

                if (empty($this->errors)) {
                    if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&id='.(int)$idHotel.'&update'.$this->table.'&conf=3&token='.
                            $this->token
                        );
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                    }
                } else {
                    $this->display = 'add';
                }
            } else {
                $this->errors[] = $this->l('Please select a hotel first.');
                $this->display = 'add';
            }
        }
    }

    public function postProcess()
    {
        if (Tools::getValue('error')) {
            if (Tools::getValue('error') == 1) {
                $msg = $this->l('Parent feature name is required.');
            } elseif (Tools::getValue('error') == 2) {
                $msg = $this->l('Position is Invalid.');
            } elseif (Tools::getValue('error') == 3) {
                $msg = $this->l('Please add atleast one Child features.');
            } elseif (Tools::getValue('error') == 4) {
                $msg = $this->l('Some error occured. Please try again.');
            }
            $this->errors[] = $this->l($msg);
            $this->context->smarty->assign("errors", $this->errors);
        }
        if (Tools::isSubmit('submit_add_btn_feature')) {
            $parentFeatureId = Tools::getValue('parent_ftr_id');
            $parentFeatureName = Tools::getValue('parent_ftr');
            $childFeatures = Tools::getValue('child_featurs');
            $pos = Tools::getValue('position');
            if (!$parentFeatureName) {
                $error = 1;
            } elseif (empty($pos) || !Validate::isUnsignedInt($pos)) {
                $error = 2;
            } elseif (!$childFeatures) {
                $error = 3;
            }
            if (!isset($error)) {
                if (isset($parentFeatureId) && $parentFeatureId) {
                    $hotelFeatures = new HotelFeatures();
                    $update_prnt_ftr = $hotelFeatures->updateHotelFeatureInfoByParentFeatureId(
                        $parentFeatureId,
                        array('name'=>$parentFeatureName, 'position'=>$pos)
                    );
                    if ($childFeaturesData = $hotelFeatures->getChildFeaturesByParentFeatureId($parentFeatureId)) {
                        $i=0;
                        foreach ($childFeaturesData as $val) {
                            $flag = 0;
                            foreach ($childFeatures as $value) {
                                if (is_numeric($value)) {
                                    if ($val['id'] == $value) {
                                        $flag = 1;
                                    }
                                } elseif ($i == 0) {
                                    $objHotelFeatures = new HotelFeatures();
                                    $objHotelFeatures->name = $value;
                                    $objHotelFeatures->active = 1;
                                    $objHotelFeatures->parent_feature_id = $parentFeatureId;
                                    $objHotelFeatures->save();
                                }
                            }
                            if (!$flag) {
                                $del_arr[] = $val['id'];
                            }
                            $i++;
                        }
                        if (isset($del_arr) && $del_arr) {
                            foreach ($del_arr as $value) {
                                $objHotelFeature = new HotelFeatures($value);
                                $objHotelFeature->delete();
                            }
                        }
                        Tools::redirectAdmin(
                            self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'&addfeatures=1&conf=4'
                        );
                    } else {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&error=4&add'.$this->table.'&token='.$this->token.'&addfeatures=1'
                        );
                    }
                } else {
                    $objHotelFeatures = new HotelFeatures();
                    $objHotelFeatures->name = $parentFeatureName;
                    $objHotelFeatures->active = 1;
                    $objHotelFeatures->position = $pos;
                    $objHotelFeatures->parent_feature_id = 0;
                    $objHotelFeatures->save();
                    if ($parentFeatureId = $objHotelFeatures->id) {
                        if ($childFeatures) {
                            foreach ($childFeatures as $val) {
                                $objHotelFeatures = new HotelFeatures();
                                $objHotelFeatures->name = $this->l($val);
                                $objHotelFeatures->active = 1;
                                $objHotelFeatures->parent_feature_id = $parentFeatureId;
                                $objHotelFeatures->save();
                            }
                            Tools::redirectAdmin(
                                self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'&addfeatures=1&conf=3'
                            );
                        }
                    } else {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&error=4&add'.$this->table.'&token='.$this->token.'&addfeatures=1'
                        );
                    }
                }
            } else {
                Tools::redirectAdmin(
                    self::$currentIndex.'&error='.$error.'&add'.$this->table.'&token='.$this->token.'&addfeatures=1'
                );
            }
        }
        parent::postProcess();
    }

    public function ajaxProcessDeleteFeature()
    {
        $dlt_id = Tools::getValue('feature_id');
        $objHotelFeatures = new HotelFeatures();
        $deleted_feature = $objHotelFeatures->deleteHotelFeatures($dlt_id);
        if ($deleted_feature) {
            die('success');
        } else {
            echo 0;
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
    }
}
