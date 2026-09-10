<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class OutOfStockException extends Exception
{
    /**
     * Create a new OutOfStockException instance.
     */
    public function __construct(string $message = "The requested item is out of stock.", int $code = 409, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render($request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $this->getMessage()], $this->getCode() ?: 409);
        }

        // Redirect the user to their account page so the error message
        // is visible in the "My Account" (customer.account) section.
        try {
            return redirect()->route('customer.account')->with('error', $this->getMessage());
        } catch (\Exception $e) {
            // Fallback to previous page if route not defined.
            return redirect()->back()->with('error', $this->getMessage());
        }
    }
}
