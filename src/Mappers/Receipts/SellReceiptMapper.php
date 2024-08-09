<?php

namespace khoroshun\Checkbox\Mappers\Receipts;

use khoroshun\Checkbox\Mappers\Receipts\Discounts\DiscountsMapper;
use khoroshun\Checkbox\Mappers\Receipts\Goods\GoodsMapper;
use khoroshun\Checkbox\Mappers\Receipts\Payments\PaymentsMapper;
use khoroshun\Checkbox\Models\Receipts\SellReceipt;

class SellReceiptMapper
{
    /**
     * @param SellReceipt $receipt
     * @return array<string, mixed>
     */
    public function objectToJson(SellReceipt $receipt): array
    {
        $output = [
            'cashier_name' => $receipt->cashier_name,
            'departament' => $receipt->departament,
            'goods' => (new GoodsMapper())->objectToJson($receipt->goods),
            'delivery' => (new DeliveryMapper())->objectToJson($receipt->delivery),
            'discounts' => (new DiscountsMapper())->objectToJson($receipt->discounts),
            'payments' => (new PaymentsMapper())->objectToJson($receipt->payments),
            'header' => $receipt->header,
            'footer' => $receipt->footer,
            'barcode' => $receipt->barcode
        ];

        if ($receipt->id) {
            $output['id'] = $receipt->id;
        }

        if ($receipt->related_receipt_id) {
            $output['related_receipt_id'] = $receipt->related_receipt_id;
        }

        return $output;
    }
}
