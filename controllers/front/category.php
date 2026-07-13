<?php
/**
 * /auto/{category} -> list of active pages within a category.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AutoCategoryModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    /** @var AutoCategory|false */
    protected $autoCategory;

    public function init()
    {
        parent::init();

        $categoryRewrite = Tools::getValue('category');
        $this->autoCategory = AutoCategory::getByLinkRewrite(
            $categoryRewrite,
            (int) $this->context->language->id
        );

        if (!$this->autoCategory) {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    public function initContent()
    {
        parent::initContent();

        $idLang = (int) $this->context->language->id;
        $pages = AutoPage::getPagesByCategory((int) $this->autoCategory->id, $idLang, true);

        foreach ($pages as &$page) {
            $page['link'] = $this->context->link->getModuleLink(
                'auto',
                'page',
                [
                    'category' => $this->autoCategory->link_rewrite,
                    'title' => $page['link_rewrite'],
                ]
            );
        }
        unset($page);

        $this->context->smarty->assign([
            'auto_category' => $this->autoCategory,
            'auto_pages' => $pages,
        ]);

        $this->setTemplate('module:auto/views/templates/front/category.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->trans('Auto', [], 'Modules.Auto.Shop'),
            'url' => $this->context->link->getModuleLink('auto', 'default'),
        ];

        if ($this->autoCategory) {
            $breadcrumb['links'][] = [
                'title' => $this->autoCategory->name,
                'url' => $this->context->link->getModuleLink(
                    'auto',
                    'category',
                    ['category' => $this->autoCategory->link_rewrite]
                ),
            ];
        }

        return $breadcrumb;
    }

    public function getCanonicalURL()
    {
        if ($this->autoCategory) {
            return $this->context->link->getModuleLink(
                'auto',
                'category',
                ['category' => $this->autoCategory->link_rewrite]
            );
        }

        return '';
    }
}
