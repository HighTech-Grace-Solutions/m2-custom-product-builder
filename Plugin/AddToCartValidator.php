<?php

namespace Buildateam\CustomProductBuilder\Plugin;

use \Magento\Checkout\Model\Session;

class AddToCartValidator
{
    /**
     * @var Session
     */
    protected $_checkoutSession;

    public function __construct(
        Session $checkoutSession
    )
    {
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\ProductVideo\Block\Product\View\Gallery|Magento\Catalog\Block\Product\View\Gallery $subject
     * @param callable $proceed
     * @return string
     */
    public function aroundValidate($subject, callable $proceed, $request)
    {
        if ($request->getHeader('X_CUSTOM_PRODUCT_BUILDER')) {
            $payload = json_decode(file_get_contents('php://input'), 1);
            foreach (['qty', 'technicalData', 'properties', 'configid'] as $paramKey) {
                if(isset($payload[$paramKey])) {
                    $request->setParam($paramKey, $payload[$paramKey]);
                }
            }

            $this->_checkoutSession->setNoCartRedirect(true);
            return true;
        }
        return $proceed($request);
    }

}