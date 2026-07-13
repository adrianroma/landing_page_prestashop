<?php
/**
 * Back-office CRUD controller for Auto categories.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminAutoCategoryController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'auto_category';
        $this->className = 'AutoCategory';
        $this->identifier = 'id_auto_category';
        $this->lang = true;
        $this->position_identifier = 'id_auto_category';
        $this->_defaultOrderBy = 'position';
        $this->_defaultOrderWay = 'ASC';
        $this->bootstrap = true;

        $this->fields_list = [
            'id_auto_category' => ['title' => $this->l('ID'), 'align' => 'center', 'width' => 40],
            'name' => ['title' => $this->l('Name')],
            'link_rewrite' => ['title' => $this->l('URL segment')],
            'position' => ['title' => $this->l('Position'), 'filter_key' => 'a!position', 'align' => 'center', 'position' => 'position'],
            'active' => ['title' => $this->l('Displayed'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'class' => 'fixed-width-sm'],
        ];

        parent::__construct();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Auto category'),
                'icon' => 'icon-folder-close',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'class' => 'copy2friendlyUrl',
                    'id' => 'name',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('URL segment'),
                    'name' => 'link_rewrite',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Used in the front URL: /auto/{this}/{page}.'),
                    'id' => 'link_rewrite',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'lang' => true,
                    'autoload_rte' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'lang' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Displayed'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Yes')],
                        ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                    ],
                ],
            ],
            'submit' => ['title' => $this->l('Save')],
        ];

        return parent::renderForm();
    }

    public function processSave()
    {
        foreach (Language::getLanguages(false) as $lang) {
            $idLang = (int) $lang['id_lang'];
            $rewrite = Tools::getValue('link_rewrite_' . $idLang);
            if ($rewrite === false || $rewrite === '') {
                $name = Tools::getValue('name_' . $idLang);
                $_POST['link_rewrite_' . $idLang] = Tools::str2url($name);
            } else {
                $_POST['link_rewrite_' . $idLang] = Tools::str2url($rewrite);
            }
        }

        return parent::processSave();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }
}
