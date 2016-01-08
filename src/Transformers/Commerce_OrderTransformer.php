<?php

namespace HttpMessagesFractalMiddleware\Transformers;

use Craft\Commerce_OrderModel;

class Commerce_OrderTransformer extends BaseTransformer
{
    /**
     * Transform
     *
     * @param Commerce_OrderModel $element Commerce Order
     *
     * @return array Commerce Order
     */
    public function transform(Commerce_OrderModel $element)
    {
        return [
            'id'                => (int) $element->id,
            'enabled'           => (bool) $element->enabled,
            'archived'          => (bool) $element->archived,
            'locale'            => $element->locale,
            'localeEnabled'     => (bool) $element->localeEnabled,
            'itemTotal'         => (float) $element->itemTotal,
            'baseDiscount'      => (float) $element->baseDiscount,
            'baseShippingCost'  => (float) $element->baseShippingCost,
            'totalPrice'        => (float) $element->totalPrice,
            'totalPaid'         => (float) $element->totalPaid,
            'dateCreated'       => $element->dateCreated,
            'dateUpdated'       => $element->dateUpdated,
            'slug'              => $element->slug,
            'uri'               => $element->uri,
            'number'            => $element->number,
            'couponCode'        => $element->couponCode,
            'orderStatusId'     => (int) $element->orderStatusId,
            'dateOrdered'       => $element->dateOrdered,
            'email'             => $element->email,
            'datePaid'          => $element->datePaid,
            'currency'          => $element->currency,
            'lastIp'            => $element->lastIp,
            'message'           => $element->message,
            'returnUrl'         => $element->returnUrl,
            'cancelUrl'         => $element->cancelUrl,
            'billingAddressId'  => (int) $element->billingAddressId,
            'shippingAddressId' => (int) $element->shippingAddressId,
            'shippingMethod'    => $element->shippingMethod,
            'paymentMethodId'   => (int) $element->paymentMethodId,
            'customerId'        => (int) $element->customerId,
        ];
    }
}
