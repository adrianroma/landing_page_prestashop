<?php
/**
 * Back-office CRUD controller for Auto pages.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminAutoPageController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'auto_page';
        $this->className = 'AutoPage';
        $this->identifier = 'id_auto_page';
        $this->lang = true;
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'position';
        $this->_defaultOrderWay = 'ASC';

        $this->_select = 'cl.`name` AS category_name';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'auto_category_lang` cl
                ON (cl.`id_auto_category` = a.`id_auto_category` AND cl.`id_lang` = ' . (int) $this->context->language->id . ')';

        $this->fields_list = [
            'id_auto_page' => ['title' => $this->l('ID'), 'align' => 'center', 'width' => 40],
            'title' => ['title' => $this->l('Title')],
            'category_name' => ['title' => $this->l('Category'), 'havingFilter' => true],
            'link_rewrite' => ['title' => $this->l('URL segment')],
            'position' => ['title' => $this->l('Position'), 'filter_key' => 'a!position', 'align' => 'center', 'position' => 'position'],
            'active' => ['title' => $this->l('Displayed'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'class' => 'fixed-width-sm'],
        ];

        parent::__construct();
    }

    public function renderForm()
    {
        $categories = AutoCategory::getCategories((int) $this->context->language->id, false);
        $categoryOptions = [];
        foreach ($categories as $category) {
            $categoryOptions[] = [
                'id_auto_category' => $category['id_auto_category'],
                'name' => $category['name'],
            ];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Auto page'),
                'icon' => 'icon-file',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->l('Category'),
                    'name' => 'id_auto_category',
                    'required' => true,
                    'options' => [
                        'query' => $categoryOptions,
                        'id' => 'id_auto_category',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                    'class' => 'copy2friendlyUrl',
                    'id' => 'title',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('URL segment'),
                    'name' => 'link_rewrite',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Used in the front URL: /auto/{category}/{this}.'),
                    'id' => 'link_rewrite',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'content',
                    'lang' => true,
                    'autoload_rte' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('LLM info'),
                    'name' => 'llm',
                    'lang' => true,
                    'hint' => $this->l('Plain-text information for LLM crawlers, published at /auto/{category}/{this page}/llm.txt.'),
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
                $title = Tools::getValue('title_' . $idLang);
                $_POST['link_rewrite_' . $idLang] = Tools::str2url($title);
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
