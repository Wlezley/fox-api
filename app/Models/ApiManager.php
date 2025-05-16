<?php

declare(strict_types=1);

namespace App\Models;

// use Nette\Database\Explorer;
use Nette\Http\Request;
use Nette\Http\Response;

class ApiManager
{
    public const OkStatus = 'ok';
    public const ErrorStatus = 'error';
    public const DefaultStatus = self::ErrorStatus;

    protected string $status = self::DefaultStatus;
    protected ?int $code = null; // SET DEFAULT to 500 or 403 or...?
    protected ?string $message = null;
    protected string $method;

    /** @var array<string> $allowedMethods */
    protected array $allowedMethods = [
        Request::Get,
        // Request::Post,
        // Request::Head,
        // Request::Put,
        // Request::Delete,
        // Request::Patch,
        // Request::Options,
    ];

    /** @var array<mixed> $data */
    protected array $data = [];

    protected Request $httpRequest;
    protected Response $httpResponse;

    // #################################
    // ###          PROCESS          ###
    // #################################

    public function processRequest(): bool
    {
        $this->method = $this->httpRequest->getMethod();

        if (in_array($this->method, $this->allowedMethods)) {
            $success = match ($this->method) {
                Request::Get => $this->processGET(),
                Request::Post => $this->processPOST(),
                Request::Head => $this->processHEAD(),
                Request::Put => $this->processPUT(),
                Request::Delete => $this->processDELETE(),
                Request::Patch => $this->processPATCH(),
                Request::Options => $this->processOPTIONS(),
                default => false
            };

            if ($success) {
                $this->status = self::OkStatus;
                return true;
            } else {
                $this->status = self::ErrorStatus;
                return false;
            }
        }

        $this->code = Response::S405_MethodNotAllowed;
        $this->message = "Method '{$this->method}' not allowed";
        return false;
    }

    protected function processGET(): bool
    {
        return false;
    }

    protected function processPOST(): bool
    {
        return false;
    }

    protected function processHEAD(): bool
    {
        return false;
    }

    protected function processPUT(): bool
    {
        return false;
    }

    protected function processPATCH(): bool
    {
        return false;
    }

    protected function processDELETE(): bool
    {
        return false;
    }

    protected function processOPTIONS(): bool
    {
        return false;
    }

    // #################################
    // ###         SET & GET         ###
    // #################################

    public function getHttpRequest(): Request
    {
        return $this->httpRequest;
    }

    public function getHttpResponse(): Response
    {
        return $this->httpResponse;
    }

    /**
     * @return array<string>
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }

    public function setError(int $code, ?string $message = null): void
    {
        $this->code = $code;
        $this->message = $message;

        $this->httpResponse->setCode($code);
    }

    /**
     * @return array<string,int|string>|null
     */
    public function getError(): ?array
    {
        $error = [];

        if ($this->code !== null) {
            $error['code'] = $this->code;
        }

        if ($this->message !== null) {
            $error['message'] = $this->message;
        }

        return $error;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMethod(): ?string
    {
        return isset($this->method) ? $this->method : null;
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array<string,mixed>
     */
    public function getDataForResponse(): array
    {
        $responseData['status'] = $this->status;

        if ($this->status == 'error') {
            $responseData['error'] = $this->getError();
            return $responseData;
        }

        return [
            'status' => $this->status,
            'data' => $this->data
        ];
    }
}
