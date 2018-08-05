<?php
class AdminTestimonialsModuleSettingController extends ModuleAdminController
{
    protected $position_identifier = 'id_testimonial_block_to_move';
    public function __construct()
    {
        $this->table = 'htl_testimonials_block_data';
        $this->className = 'WkHotelTestimonialData';
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'position';
        $this->context = Context::getContext();
        $this->identifier  = 'id_testimonial_block';

        parent::__construct();

        $this->fields_options = array(
            'featuresmodulesetting' => array(
                'title' =>    $this->l('Hotel Testimonials Setting'),
                'fields' =>    array(
                    'HOTEL_TESIMONIAL_BLOCK_HEADING' => array(
                        'title' => $this->l('Testimonial Blog Title'),
                        'type' => 'text',
                        'required' => 'true',
                        'id' => 'HOTEL_TESIMONIAL_BLOCK_HEADING',
                        'hint' => $this->l('Testimonial Block Heading. Ex. Guest Testimonials.'),
                    ),
                    'HOTEL_TESIMONIAL_BLOCK_CONTENT' => array(
                        'title' => $this->l('Testimonial Blog Description'),
                        'type' => 'textarea',
                        'required' => 'true',
                        'id' => 'HOTEL_TESIMONIAL_BLOCK_CONTENT',
                        'rows' => '4',
                        'cols' => '2',
                        'hint' => $this->l('Testimonial Block Detail.'),
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        $this->fields_list = array(
            'id_testimonial_block' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'date_upd' => array(
                'title' => $this->l('Person Image'),
                'align' => 'center',
                'callback' => 'getTestimonialImage',
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'align' => 'center',
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
                'class' => 'fixed-width-xs'
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
            'enableSelection' => array(
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ),
        );
    }

    public function getTestimonialImage($echo, $row)
    {
        $image = '';
        if ($echo) {
            $imgUrl = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotels_testimonials_img/'.
            $row['id_testimonial_block'].'.jpg';
            if (file_exists($imgUrl)) {
                $modImgUrl = _MODULE_DIR_.$this->module->name.'/views/img/hotels_testimonials_img/'.
                $row['id_testimonial_block'].'.jpg';
                $image = "<img class='img-thumbnail img-responsive' style='max-width:70px' src='".$modImgUrl."'>";
            }
        }
        if ($image == '') {
            $modImgUrl = _MODULE_DIR_.$this->module->name.'/views/img/default-user.jpg';
            $image = "<img class='img-thumbnail img-responsive' style='max-width:70px' src='".$modImgUrl."'>";
        }
        return $image;
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $ps_img_url = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotels_testimonials_img/'.$obj->id.'.jpg';
        if ($img_exist = file_exists($ps_img_url)) {
            $mod_img_url = _MODULE_DIR_.$this->module->name.'/views/img/hotels_testimonials_img/'.$obj->id.'.jpg';
            $image = "<img class='img-thumbnail img-responsive' style='max-width:100px' src='".$mod_img_url."'>";
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Hotel Testimonial Configuration'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Person Name'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => $this->l('Testimonial person name')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Person\'s Designation'),
                    'name' => 'designation',
                    'required' => true,
                    'hint' => $this->l('Testimonial person Designation')
                ),
                array(
                    'type' => 'textarea',
                    'rows' => '4',
                    'label' => $this->l('Testimonial Description'),
                    'name' => 'testimonial_content',
                    'required' => true,
                    'hint' => $this->l('Testimonial Content')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Person image'),
                    'name' => 'testimonial_image',
                    'display_image' => true,
                    'image' => $img_exist ? $image : false,
                    'hint' => $this->l('Upload an image of the person to whom this testimonial belongs.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save')
            ));

        return parent::renderForm();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new Testimonial')
        );
    }

    public function processSave()
    {
        $testimonial_id = Tools::getValue('id_testimonial_block');
        $person_name = Tools::getValue('name');
        $person_designation = Tools::getValue('designation');
        $testimonial_content = Tools::getValue('testimonial_content');
        if (!$person_name) {
            $this->errors[] = $this->l('Person\'s Name is a required field.');
        }
        if (!$person_designation) {
            $this->errors[] = $this->l('Person\'s Designation is a required field.');
        }
        if ($testimonial_content == '') {
            $this->errors[] = $this->l('Testimonial content is a required field.');
        }
        if (isset($_FILES['testimonial_image']) && $_FILES['testimonial_image']['tmp_name']) {
            $error = HotelImage::validateImage($_FILES['testimonial_image']);
            if ($error) {
                $this->errors[] = $this->l('Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
            }
        }

        if (!count($this->errors)) {
            if ($testimonial_id) {
                $objTestimonialData = new WkHotelTestimonialData($testimonial_id);
            } else {
                $objTestimonialData = new WkHotelTestimonialData();
                $objTestimonialData->position = WkHotelTestimonialData::getHigherPosition();
            }

            $objTestimonialData->name = $person_name;
            $objTestimonialData->designation = $person_designation;
            $objTestimonialData->testimonial_content = $testimonial_content;
            $objTestimonialData->active = Tools::getValue('active');
            if ($objTestimonialData->save()) {
                if ($_FILES['testimonial_image']['size']) {
                    $testimonial_img_path = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotels_testimonials_img/'.
                    $objTestimonialData->id.'.jpg';
                    ImageManager::resize($_FILES['testimonial_image']['tmp_name'], $testimonial_img_path);
                }
            }
            if (Tools::getValue("id")) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        } else {
            if (Tools::getValue("id")) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionshtl_features_block_data')) {
            $testimonial_main_blog_title = Tools::getValue('HOTEL_TESIMONIAL_BLOCK_HEADING');
            $testimonial_main_blog_content = Tools::getValue('HOTEL_TESIMONIAL_BLOCK_CONTENT');

            if (!$testimonial_main_blog_title) {
                $this->errors[] = $this->l('Testimonila blog title is a required field.');
            }
            if (!$testimonial_main_blog_content) {
                $this->errors[] = $this->l('Testimonial blog desription is a required field.');
            }
        }
        parent::postProcess();
    }

    // update positions of membership
    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $idTestimonialBlock = (int) Tools::getValue('id');
        $positions = Tools::getValue('testimonial_block');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idTestimonialBlock) {
                if ($objTestimonialBlock = new WkHotelTestimonialData((int) $pos[2])) {
                    if (isset($position)
                        && $objTestimonialBlock->updatePosition($way, $position, $idTestimonialBlock)
                    ) {
                        echo 'ok position '.(int) $position.' for testimonial block '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update testimonial block position '.
                        (int) $idTestimonialBlock.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This testimonial block ('.(int) $idTestimonialBlock.
                    ') can t be loaded"}';
                }
                break;
            }
        }
    }
}
