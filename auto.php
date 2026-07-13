<?php
/**
 * Module: Auto
 *
 * Content pages organized by category, mirroring the structure of PrestaShop's
 * built-in CMS feature (CMS / CMSCategory), exposed under the front URL
 * pattern: /auto/{category}/{title} (e.g. /auto/lugares/segovia).
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/AutoCategory.php';
require_once __DIR__ . '/classes/AutoPage.php';

class Auto extends Module
{
    /**
     * Admin controllers registered as back-office tabs by this module.
     *
     * @var string[]
     */
    public $adminControllers = [
        'AdminAutoCategory',
        'AdminAutoPage',
    ];

    public function __construct()
    {
        $this->name = 'auto';
        $this->tab = 'content_management';
        $this->version = '1.0.0';
        $this->author = 'RecoAutos';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Auto');
        $this->description = $this->l('Content pages organized by category, published under /auto/{category}/{title}.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall? All Auto categories and pages will be deleted.');
    }

    public function install()
    {
        require_once __DIR__ . '/sql/install.php';

        return parent::install()
            && $this->installTabs()
            && $this->registerHook('moduleRoutes')
            && $this->registerHook('displayHeader')
            && Tools::clearSmartyCache();
    }

    public function uninstall()
    {
        require_once __DIR__ . '/sql/uninstall.php';

        return $this->uninstallTabs()
            && parent::uninstall();
    }

    /**
     * Creates the back-office menu: a top-level "Auto" entry with two
     * sub-tabs (Categories, Pages).
     */
    protected function installTabs()
    {
        $result = true;

        if (!Tab::getIdFromClassName('AdminAuto')) {
            $parentTab = new Tab();
            $parentTab->class_name = 'AdminAuto';
            $parentTab->module = $this->name;
            $parentTab->id_parent = 0;
            $parentTab->icon = 'directions_car';
            $parentTab->name = array_fill_keys(
                array_map(function ($lang) {
                    return (int) $lang['id_lang'];
                }, Language::getLanguages(false)),
                'Auto'
            );
            $result = $result && $parentTab->add();
        }

        $idParent = (int) Tab::getIdFromClassName('AdminAuto');

        $subTabs = [
            'AdminAutoCategory' => 'Auto - Categories',
            'AdminAutoPage' => 'Auto - Pages',
        ];

        foreach ($subTabs as $className => $label) {
            if (Tab::getIdFromClassName($className)) {
                continue;
            }

            $tab = new Tab();
            $tab->class_name = $className;
            $tab->module = $this->name;
            $tab->id_parent = $idParent;
            $tab->name = array_fill_keys(
                array_map(function ($lang) {
                    return (int) $lang['id_lang'];
                }, Language::getLanguages(false)),
                $label
            );
            $result = $result && $tab->add();
        }

        return $result;
    }

    protected function uninstallTabs()
    {
        $result = true;

        foreach (array_merge($this->adminControllers, ['AdminAuto']) as $className) {
            $idTab = (int) Tab::getIdFromClassName($className);
            if ($idTab) {
                $tab = new Tab($idTab);
                $result = $result && $tab->delete();
            }
        }

        return $result;
    }

    /**
     * Registers the front URL scheme:
     *   /auto                     -> list of categories
     *   /auto/{category}          -> list of pages within a category
     *   /auto/{category}/{title}  -> a single page
     */
    public function hookModuleRoutes($params)
    {
        return [
            'module-auto-default' => [
                'controller' => 'default',
                'rule' => 'auto',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'auto',
                    'controller' => 'default',
                ],
            ],
            'module-auto-category' => [
                'controller' => 'category',
                'rule' => 'auto/{category}',
                'keywords' => [
                    'category' => ['regexp' => '[_a-zA-Z0-9\-]+', 'param' => 'category'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'auto',
                    'controller' => 'category',
                ],
            ],
            'module-auto-page' => [
                'controller' => 'page',
                'rule' => 'auto/{category}/{title}',
                'keywords' => [
                    'category' => ['regexp' => '[_a-zA-Z0-9\-]+', 'param' => 'category'],
                    'title' => ['regexp' => '[_a-zA-Z0-9\-]+', 'param' => 'title'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'auto',
                    'controller' => 'page',
                ],
            ],
        ];
    }

    public function hookDisplayHeader($params)
    {
        // Reserved for front-office asset injection (CSS/JS) if the theme needs it.
    }

    /**
     * @return string Admin link to the categories list, used by getContent()
     */
    public function getContent()
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminAutoCategory')
        );
    }
}
