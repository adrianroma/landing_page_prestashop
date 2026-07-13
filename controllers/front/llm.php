<?php
/**
 * /auto/{category}/{title}/llm.txt -> plain-text "llm" content of a page.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AutoLlmModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    public function initContent()
    {
        $idLang = (int) $this->context->language->id;

        $autoCategory = AutoCategory::getByLinkRewrite(Tools::getValue('category'), $idLang);
        $autoPage = false;

        if ($autoCategory) {
            $autoPage = AutoPage::getByLinkRewrite(
                Tools::getValue('title'),
                (int) $autoCategory->id,
                $idLang
            );
        }

        if (!$autoCategory || !$autoPage) {
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: text/plain; charset=utf-8');
            exit;
        }

        header('Content-Type: text/plain; charset=utf-8');
        exit((string) $autoPage->llm);
    }
}
