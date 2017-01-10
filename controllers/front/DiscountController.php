<?php
/**
 * 2007-2016 PrestaShop
 *
 * Thirty Bees is an extension to the PrestaShop e-commerce software developed by PrestaShop SA
 * Copyright (C) 2017 Thirty Bees
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@thirtybees.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.thirtybees.com for more information.
 *
 *  @author    Thirty Bees <contact@thirtybees.com>
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2017 Thirty Bees
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  PrestaShop is an internationally registered trademark & property of PrestaShop SA
 */

class DiscountControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'discount';
    public $authRedirection = 'discount';
    public $ssl = true;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $cart_rules = CartRule::getCustomerCartRules($this->context->language->id, $this->context->customer->id, true, false, true);
        $nb_cart_rules = count($cart_rules);

        foreach ($cart_rules as $key => &$discount ) {
            if ($discount['quantity_for_user'] === 0) {
                unset($cart_rules[$key]);
            }


            $discount['value'] = Tools::convertPriceFull(
                                            $discount['value'],
                                            new Currency((int)$discount['reduction_currency']),
                                            new Currency((int)$this->context->cart->id_currency)
                                        );
            if ($discount['gift_product'] !== 0) {
                $product = new Product((int) $discount['gift_product']);
                if (isset($product->name)) {
                    $discount['gift_product_name'] = current($product->name);
                }
            }
        }

        $this->context->smarty->assign(
            [
                                            'nb_cart_rules' => (int)$nb_cart_rules,
                                            'cart_rules' => $cart_rules,
                                            'discount' => $cart_rules,
                                            'nbDiscounts' => (int)$nb_cart_rules
            ]
                                        );
        $this->setTemplate(_PS_THEME_DIR_.'discount.tpl');
    }
}
