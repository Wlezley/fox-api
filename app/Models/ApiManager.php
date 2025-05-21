<?php

declare(strict_types=1);

namespace App\Models\ApiManager;

use Nette\Http\Request;
use Nette\Http\Response;

/**
 * Base class for handling REST API requests.
 *
 * This class provides request method dispatching, error management, response formatting
 * and data access for use in derived API handler classes.
 */
class ApiManager
{
    public const OkStatus = 'ok';
    public const ErrorStatus = 'error';
    public const DefaultStatus = self::ErrorStatus;

    protected string $status = self::DefaultStatus;
    protected ?int $code = null;
    protected ?string $message = null;
    protected string $method;

    /** @var array<string> $allowedMethods */
    protected array $allowedMethods = [
        // Possible values:
        // ----------------
        // Request::Get,
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

    /**
     * Main entry point for processing the HTTP request.
     *
     * Dispatches based on the current HTTP method and handles allowed method validation.
     *
     * @return bool True on successful method-specific processing, otherwise false.
     */
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

    /**
     * Handles the HTTP GET request.
     *
     * Override in subclass to implement GET-specific logic.
     *
     * @return bool True if the request was handled successfully, otherwise false.
     */
    protected function processGET(): bool
    {
        return false;
    }

    /**
     * Handles the HTTP POST request.
     *
     * Override in subclass to implement POST-specific logic.
     *
     * @return bool True if the request was handled successfully, otherwise false.
     */
    protected function processPOST(): bool
    {
        return false;
    }

    /**
     * Handles the HTTP HEAD request.
     *
     * Override in subclass to implement HEAD-specific logic.
     *
     * @return bool True if the request was handled successfully, otherwise false.
     */
    protected function processHEAD(): bool
    {
        $this->httpResponse->setCode(Response::S200_OK);
        return true;
    }

    /**
     * Handles the HTTP PUT request.
     *
     * Override in subclass to implement PUT-specific logic.
     *
     * @return bool True if the request was handled successfully, otherwise false.
     */
    protected function processPUT(): bool
    {
        return false;
    }

    /**
     * Handles the HTTP PATCH request.
     *
     * Override in subclass to implement PATCH-specific logic.
     *
     * @return bool True if the request was handled successfully, otherwise false.
     */
    protected function processPATCH(): bool
    {
        return false;
    }

    /**
     * Handles the HTTP DELETE request.
     *
     * Override in subclass to implement DELETE-specific logic.
     *
     * @return bool True if the request was handled successfully, otherwise false.
     */
    protected function processDELETE(): bool
    {
        return false;
    }

    /**
     * Handles the HTTP OPTIONS request.
     *
     * Override in subclass to implement OPTIONS-specific logic.
     *
     * @return bool True if the request was handled successfully, otherwise false.
     */
    protected function processOPTIONS(): bool
    {
        $this->setError(Response::S200_OK, 'Allowed methods: ' . implode(', ', $this->allowedMethods));
        return true;
    }

    // #################################
    // ###         SET & GET         ###
    // #################################

    /**
     * Returns the current HTTP request object.
     *
     * @return Request The Nette HTTP request object.
     */
    public function getHttpRequest(): Request
    {
        return $this->httpRequest;
    }

    /**
     * Returns the current HTTP response object.
     *
     * @return Response The Nette HTTP response object.
     */
    public function getHttpResponse(): Response
    {
        return $this->httpResponse;
    }

    /**
     * Returns a list of allowed HTTP methods for this API handler.
     *
     * @return array<string> Array of allowed HTTP method names (e.g. "GET", "POST").
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }

    /**
     * Sets the error response state including HTTP code and optional message.
     *
     * @param int $code HTTP status code.
     * @param string|null $message Optional error message.
     */
    public function setError(int $code, ?string $message = null): void
    {
        $this->code = $code;
        $this->message = $message;

        $this->httpResponse->setCode($code);
    }

    /**
     * Returns the current error state as an associative array or null if no error.
     *
     * @return array<string,int|string> Associative array with 'code' and/or 'message', or empty array if no error.
     */
    public function getError(): array
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

    /**
     * Returns the current processing status.
     *
     * @return string Either 'ok' or 'error'.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Returns the current HTTP request method name (e.g. "GET"), or null if not set.
     *
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return isset($this->method) ? $this->method : null;
    }

    /**
     * Returns raw internal response data set by the API logic.
     *
     * @return array<mixed> Arbitrary associative data array.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Formats response payload for output to client.
     *
     * Includes status and data or error, depending on state.
     *
     * @return array<string,mixed> Formatted response payload.
     */
    public function getDataForResponse(): array
    {
        if ($this->status == 'ok') {
            return [
                'status' => $this->status,
                'data' => $this->data
            ];
        }

        return [
            'status' => $this->status,
            'error' => $this->getError()
        ];
    }
}
