<?php

namespace khoroshun\Checkbox;

use GuzzleHttp\Client;
use khoroshun\Checkbox\Errors\AlreadyOpenedShift;
use khoroshun\Checkbox\Errors\BadRequest;
use khoroshun\Checkbox\Errors\BadRequestExceptionFactory;
use khoroshun\Checkbox\Errors\EmptyResponse;
use khoroshun\Checkbox\Errors\InvalidCredentials;
use khoroshun\Checkbox\Errors\NoActiveShift;
use khoroshun\Checkbox\Errors\Validation;
use khoroshun\Checkbox\Mappers\Cashier\CashierMapper;
use khoroshun\Checkbox\Mappers\CashRegisters\CashRegisterInfoMapper;
use khoroshun\Checkbox\Mappers\CashRegisters\CashRegisterMapper;
use khoroshun\Checkbox\Mappers\CashRegisters\CashRegistersMapper;
use khoroshun\Checkbox\Mappers\Receipts\ReceiptMapper;
use khoroshun\Checkbox\Mappers\Receipts\ReceiptsMapper;
use khoroshun\Checkbox\Mappers\Receipts\SellReceiptMapper;
use khoroshun\Checkbox\Mappers\Receipts\ServiceReceiptMapper;
use khoroshun\Checkbox\Mappers\Receipts\Taxes\GoodTaxesMapper;
use khoroshun\Checkbox\Mappers\Reports\ReportsMapper;
use khoroshun\Checkbox\Mappers\Shifts\CloseShiftMapper;
use khoroshun\Checkbox\Mappers\Shifts\CreateShiftMapper;
use khoroshun\Checkbox\Mappers\Shifts\ShiftMapper;
use khoroshun\Checkbox\Mappers\Shifts\ShiftsMapper;
use khoroshun\Checkbox\Mappers\Shifts\ZReportMapper;
use khoroshun\Checkbox\Mappers\Transactions\TransactionMapper;
use khoroshun\Checkbox\Mappers\Transactions\TransactionsMapper;
use khoroshun\Checkbox\Models\Cashier\Cashier;
use khoroshun\Checkbox\Models\CashRegisters\CashRegister;
use khoroshun\Checkbox\Models\CashRegisters\CashRegisterInfo;
use khoroshun\Checkbox\Models\CashRegisters\CashRegisters;
use khoroshun\Checkbox\Models\CashRegisters\CashRegistersQueryParams;
use khoroshun\Checkbox\Models\Receipts\Receipt;
use khoroshun\Checkbox\Models\Receipts\Receipts;
use khoroshun\Checkbox\Models\Receipts\ReceiptsQueryParams;
use khoroshun\Checkbox\Models\Receipts\SellReceipt;
use khoroshun\Checkbox\Models\Receipts\ServiceReceipt;
use khoroshun\Checkbox\Models\Receipts\Taxes\GoodTaxes;
use khoroshun\Checkbox\Models\Reports\PeriodicalReportQueryParams;
use khoroshun\Checkbox\Models\Reports\Reports;
use khoroshun\Checkbox\Models\Reports\ReportsQueryParams;
use khoroshun\Checkbox\Models\Shifts\CloseShift;
use khoroshun\Checkbox\Models\Shifts\CreateShift;
use khoroshun\Checkbox\Models\Shifts\Shift;
use khoroshun\Checkbox\Models\Shifts\Shifts;
use khoroshun\Checkbox\Models\Shifts\ShiftsQueryParams;
use khoroshun\Checkbox\Models\Shifts\ZReport;
use khoroshun\Checkbox\Models\Transactions\Transaction;
use khoroshun\Checkbox\Models\Transactions\Transactions;
use khoroshun\Checkbox\Models\Transactions\TransactionsQueryParams;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CheckboxJsonApi
{
    /** @var Routes $routes */
    private $routes;

    /** @var Client $guzzleClient */
    private $guzzleClient;

    /** @var int $connectTimeout */
    private $connectTimeout;

    /** @var Config $config */
    private $config = null;

    /**
     * Request options.
     * @var array<string, mixed> $requestOptions
     */
    private $requestOptions;

    public const METHOD_GET = 'get';
    public const METHOD_POST = 'post';
    public const METHOD_PATCH = 'patch';

    /**
     * Constructor
     *
     * @param Config $config
     * @param int $connectTimeoutSeconds
     *
     */
    public function __construct(Config $config, int $connectTimeoutSeconds = 5)
    {
        $this->config = $config;
        $this->connectTimeout = $connectTimeoutSeconds;
        $this->routes = new Routes($this->config->get(Config::API_URL));

        $this->guzzleClient = new Client([
            'verify' => false,
            'http_errors' => false
        ]);

        $this->requestOptions = [
            'connect_timeout' => $this->connectTimeout,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-License-Key' => $this->config->get('licenseKey')
            ]
        ];
    }

    private function setHeadersWithToken(string $token): void
    {
        $this->requestOptions['headers']['Authorization'] = 'Bearer ' . $token;
    }

    /**
     * @param mixed $json
     * @param int $statusCode
     * @return void
     */
    private function validateResponseStatus($json, int $statusCode): void
    {
        switch ($statusCode) {
            case 400:
                $message = $json['message'] ?? '';

                throw BadRequestExceptionFactory::getExceptionByMessage($message);
            case 401:
            case 403:
                throw new InvalidCredentials($json['message']);
            case 422:
                throw new Validation($json);
        }

        if (!empty($json['message'])) {
            throw new \Exception($json['message']);
        }
    }

    // start Cashier methods //

    public function signInCashier(): void
    {
        $options = $this->requestOptions;
        $options['body'] = \json_encode([
            'login' => $this->config->get(Config::LOGIN),
            'password' => $this->config->get(Config::PASSWORD)
        ]);

        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->signInCashier(),
            $options
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        if (is_null($jsonResponse)) {
            throw new EmptyResponse('Запрос вернул пустой результат');
        }

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        $this->setHeadersWithToken($jsonResponse['access_token']);
    }

    public function signOutCashier(): void
    {
        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->signOutCashier(),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());
    }

    /*
    public function signInCashierViaSignature(string $signature)
    {
        $options = $this->requestOptions;
        $options['body'] = \json_encode([
            'signature' => $signature
        ]);

        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->signInCashierViaSignature(),
            $options
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return $jsonResponse;
    }
    */


    public function signInCashierViaPinCode(): void
    {
        $options = $this->requestOptions;
        $options['body'] = \json_encode([
            'pin_code' => $this->config->get(Config::PINCODE)
        ]);

        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->signInCashierViaPinCode(),
            $options
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        if (is_null($jsonResponse)) {
            throw new EmptyResponse('Запрос вернул пустой результат');
        }

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        $this->setHeadersWithToken($jsonResponse['access_token']);
    }


    public function getCashierProfile(): ?Cashier
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getCashierProfile(),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new CashierMapper())->jsonToObject($jsonResponse);
    }

    public function getCashierShift(): ?Shift
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getCashierShift(),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ShiftMapper())->jsonToObject($jsonResponse);
    }

    /**
     * @return mixed
     */
    public function pingTaxServiceAction()
    {
        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->pingTaxServiceAction(),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return $jsonResponse;
    }

    // end Cashier methods //

    // start Shift methods //

    public function getShifts(ShiftsQueryParams $queryParams = null): ?Shifts
    {
        if (is_null($queryParams)) {
            $queryParams = new ShiftsQueryParams();
        }

        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getShifts($queryParams),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ShiftsMapper())->jsonToObject($jsonResponse);
    }

    public function createShift(): ?CreateShift
    {
        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->createShift(),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new CreateShiftMapper())->jsonToObject($jsonResponse);
    }

    public function getShift(string $shiftId): ?Shift
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getShift($shiftId),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ShiftMapper())->jsonToObject($jsonResponse);
    }

    public function closeShift(): ?CloseShift
    {
        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->closeShift(),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new CloseShiftMapper())->jsonToObject($jsonResponse);
    }

    // end Shift methods //

    // start cash registers methods //

    public function getCashRegisters(CashRegistersQueryParams $queryParams = null): ?CashRegisters
    {
        if (is_null($queryParams)) {
            $queryParams = new CashRegistersQueryParams();
        }

        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getCashRegisters($queryParams),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new CashRegistersMapper())->jsonToObject($jsonResponse);
    }

    public function getCashRegister(string $registerId): ?CashRegister
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getCashRegister($registerId),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new CashRegisterMapper())->jsonToObject($jsonResponse);
    }

    public function getCashRegisterInfo(): ?CashRegisterInfo
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getCashRegisterInfo(),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new CashRegisterInfoMapper())->jsonToObject($jsonResponse);
    }

    // end cash registers methods //

    // start receipts methods //

    public function getReceipts(ReceiptsQueryParams $queryParams = null): ?Receipts
    {
        if (is_null($queryParams)) {
            $queryParams = new ReceiptsQueryParams();
        }
        $this->routes->getReceipts($queryParams);

        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReceipts($queryParams),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ReceiptsMapper())->jsonToObject($jsonResponse);
    }

    public function getReceipt(string $receiptId): ?Receipt
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReceipt($receiptId),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ReceiptMapper())->jsonToObject($jsonResponse);
    }

    public function createSellReceipt(SellReceipt $receipt): ?Receipt
    {
        $options = $this->requestOptions;
        $options['body'] = \json_encode((new SellReceiptMapper())->objectToJson($receipt));

        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->createSellReceipt(),
            $options
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ReceiptMapper())->jsonToObject($jsonResponse);
    }

    public function createServiceReceipt(ServiceReceipt $receipt): ?Receipt
    {
        $options = $this->requestOptions;
        $options['body'] = \json_encode((new ServiceReceiptMapper())->objectToJson($receipt));

        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->createServiceReceipt(),
            $options
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ReceiptMapper())->jsonToObject($jsonResponse);
    }

    public function getReceiptPdf(string $receiptId): string
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReceiptPdf($receiptId),
            $this->requestOptions
        );

        $responseContents = $response->getBody()->getContents();

        $jsonResponse = json_decode($responseContents, true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return $responseContents;
    }

    public function getReceiptHtml(string $receiptId): string
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReceiptHtml($receiptId),
            $this->requestOptions
        );

        $responseContents = $response->getBody()->getContents();

        $jsonResponse = json_decode($responseContents, true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return $responseContents;
    }

    public function getReceiptText(string $receiptId): string
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReceiptText($receiptId),
            $this->requestOptions
        );

        $responseContents = $response->getBody()->getContents();

        $jsonResponse = json_decode($responseContents, true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return $responseContents;
    }

    public function getReceiptQrCodeImage(string $receiptId): string
    {
        $options = $this->requestOptions;
        $options['headers']['Content-Type'] = 'image/png';

        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReceiptQrCodeImage($receiptId),
            $options
        );

        $responseContents = $response->getBody()->getContents();

        $jsonResponse = json_decode($responseContents, true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return $responseContents;
    }

    public function getReceiptImagePng(string $receiptId, int $width = 30, int $paperWidth = 58): string
    {
        $options = $this->requestOptions;
        $options['headers']['Content-Type'] = 'image/png';

        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReceiptImagePng($receiptId, $width, $paperWidth),
            $options
        );

        $responseContents = $response->getBody()->getContents();

        $jsonResponse = json_decode($responseContents, true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return $responseContents;
    }

    public function getReceiptImagePngLink(string $receiptId, int $width = 30, int $paperWidth = 58): string
    {
        return $this->routes->getReceiptImagePng($receiptId, $width, $paperWidth);
    }

    // end receipts methods //

    // start taxes methods //

    public function getAllTaxes(): ?GoodTaxes
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getAllTaxes(),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new GoodTaxesMapper())->jsonToObject($jsonResponse);
    }

    // end taxes methods //

    // start report methods //

    public function createXReport(): ?ZReport
    {
        $options = $this->requestOptions;
        $options['headers']['X-Client-Name'] = 'khoroshun Custom SDK';
        $options['headers']['X-Client-Version'] = '1.0.0';

        $response = $this->sendRequest(
            self::METHOD_POST,
            $this->routes->createXReport(),
            $options
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ZReportMapper())->jsonToObject($jsonResponse);
    }

    public function getReport(string $reportId): ?ZReport
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReport($reportId),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ZReportMapper())->jsonToObject($jsonResponse);
    }

    public function getReportText(string $reportId, int $printArea = 42): string
    {
        if ($printArea < 10 or $printArea > 250) {
            throw new \Exception('That print area is not valid');
        }

        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReportText($reportId, $printArea),
            $this->requestOptions
        );

        $responseContents = $response->getBody()->getContents();

        $jsonResponse = json_decode($responseContents, true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return $responseContents;
    }

    public function getPeriodicalReport(PeriodicalReportQueryParams $queryParams): string
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getPeriodicalReport($queryParams),
            $this->requestOptions
        );

        $responseContents = $response->getBody()->getContents();

        $jsonResponse = json_decode($responseContents, true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return $responseContents;
    }

    public function getReports(ReportsQueryParams $queryParams): ?Reports
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getReports($queryParams),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new ReportsMapper())->jsonToObject($jsonResponse);
    }


    // end report methods //

    // start transaction methods //

    public function getTransaction(string $transactionId): ?Transaction
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getTransaction($transactionId),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new TransactionMapper())->jsonToObject($jsonResponse);
    }

    public function getTransactions(TransactionsQueryParams $queryParams): ?Transactions
    {
        $response = $this->sendRequest(
            self::METHOD_GET,
            $this->routes->getTransactions($queryParams),
            $this->requestOptions
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new TransactionsMapper())->jsonToObject($jsonResponse);
    }


    public function updateTransaction(string $transactionId, string $requestSignature): ?Transaction
    {
        $options = $this->requestOptions;
        $options['body'] = \json_encode([
            'request_signature' => $requestSignature
        ]);

        $response = $this->sendRequest(
            self::METHOD_PATCH,
            $this->routes->updateTransaction($transactionId),
            $options
        );

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $this->validateResponseStatus($jsonResponse, $response->getStatusCode());

        return (new TransactionMapper())->jsonToObject($jsonResponse);
    }

    // end transaction methods //

    /**
     * @param array<mixed> $options
     */
    protected function sendRequest(string $method, string $uri = '', array $options = []): ResponseInterface
    {
        return $this->guzzleClient->request($method, $uri, $options);
    }
}
