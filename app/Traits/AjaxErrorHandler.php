<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

trait AjaxErrorHandler
{
    /**
     * Handle validation errors for AJAX requests
     */
    protected function handleValidationError(Request $request, ValidationException $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal. Periksa data yang dimasukkan.',
            'errors' => $e->errors(),
            'error_type' => 'validation'
        ], 422);
    }

    /**
     * Handle database errors for AJAX requests
     */
    protected function handleDatabaseError(Request $request, QueryException $e)
    {
        $errorMessage = 'Terjadi kesalahan database.';
        
        // Check for specific database errors
        if (str_contains($e->getMessage(), 'Connection refused')) {
            $errorMessage = 'Tidak dapat terhubung ke database.';
        } elseif (str_contains($e->getMessage(), 'timeout')) {
            $errorMessage = 'Timeout koneksi database.';
        }

        return response()->json([
            'success' => false,
            'message' => $errorMessage,
            'error_type' => 'database',
            'debug' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }

    /**
     * Handle general errors for AJAX requests
     */
    protected function handleGeneralError(Request $request, \Exception $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
            'error_type' => 'general',
            'debug' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }

    /**
     * Handle all types of errors for AJAX requests
     */
    protected function handleAjaxError(Request $request, \Exception $e)
    {
        if ($e instanceof ValidationException) {
            return $this->handleValidationError($request, $e);
        }
        
        if ($e instanceof QueryException) {
            return $this->handleDatabaseError($request, $e);
        }
        
        return $this->handleGeneralError($request, $e);
    }
}
