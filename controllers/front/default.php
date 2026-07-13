<?php
/**
 * /auto -> list of active Auto categories.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AutoDefaultModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    public function initContent()
    {
        parent::initContent();

        $idLang = (int) $this->context->language->id;
        $categories = AutoCategory::getCategories($idLang, true);

        foreach ($categories as &$category) {
            $category['link'] = $this->context->link->getModuleLink(
                'auto',
                'category',
                ['category' => $category['link_rewrite']]
            );
        }
        unset($category);

        $this->context->smarty->assign([
            'auto_categories' => $categories,
        ]);

        $this->setTemplate('module:auto/views/templates/front/default.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->trans('Auto', [], 'Modules.Auto.Shop'),
            'url' => $this->context->link->getModuleLink('auto', 'default'),
        ];

        return $breadcrumb;
    }
}
