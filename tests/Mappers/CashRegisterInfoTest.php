<?php

declare(strict_types=1);

namespace khoroshun\Checkbox\Mappers;

use khoroshun\Checkbox\Mappers\CashRegisters\CashRegisterInfoMapper;
use PHPUnit\Framework\TestCase;

class CashRegisterInfoTest extends TestCase
{
    /** @var  string $jsonString */
    private $jsonString;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->jsonString = '{
           "id":"3e650f3e-09b9-44e4-baea-f40f143cbb00",
           "fiscal_number":"4000037367",
           "created_at":"2020-09-28T10:57:51+00:00",
           "updated_at":"2020-11-06T13:04:56.401242+00:00",
           "address":"УКРАЇНА, АР КРИММ. СІМФЕРОПОЛЬ СЕЛИЩЕ БІТУМНЕ, test 1",
           "title":"test 1",
           "offline_mode": false,
           "stay_offline": false,
           "has_shift": true,
           "documents_state":{
              "last_receipt_code":67,
              "last_report_code":28,
              "last_z_report_code":28
           }
        }';
    }

    public function testMapCreateShiftWithNull(): void
    {
        $this->assertNull(
            (new CashRegisterInfoMapper())->jsonToObject(null)
        );
    }

    public function testRegister(): void
    {
        $jsonResponse = json_decode($this->jsonString, true);

        $mapped = (new CashRegisterInfoMapper())->jsonToObject($jsonResponse);

        $this->assertEquals(
            '3e650f3e-09b9-44e4-baea-f40f143cbb00',
            $mapped->id
        );
        $this->assertEquals(
            28,
            $mapped->documents_state->last_z_report_code
        );
    }
}
