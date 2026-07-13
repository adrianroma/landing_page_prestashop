<?php
/**
 * /auto/{category}/{title} -> a single Auto page.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AutoPageModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    /** @var AutoCategory|false */
    protected $autoCategory;

    /** @var AutoPage|false */
    protected $autoPage;

    public function init()
    {
        parent::init();

        $idLang = (int) $this->context->language->id;

        $this->autoCategory = AutoCategory::getByLinkRewrite(Tools::getValue('category'), $idLang);

        if ($this->autoCategory) {
            $this->autoPage = AutoPage::getByLinkRewrite(
                Tools::getValue('title'),
                (int) $this->autoCategory->id,
                $idLang
            );
        }

        if (!$this->autoCategory || !$this->autoPage) {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            'auto_category' => $this->autoCategory,
            'auto_page' => $this->autoPage,
        ]);

        $this->setTemplate('module:auto/views/templates/front/page.tpl');
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

        if ($this->autoPage) {
            $breadcrumb['links'][] = [
                'title' => $this->autoPage->title,
                'url' => $this->getCanonicalURL(),
            ];
        }

        return $breadcrumb;
    }

    public function getCanonicalURL()
    {
        if ($this->autoCategory && $this->autoPage) {
            return $this->context->link->getModuleLink(
                'auto',
                'page',
                [
                    'category' => $this->autoCategory->link_rewrite,
                    'title' => $this->autoPage->link_rewrite,
                ]
            );
        }

        return '';
    }
}
