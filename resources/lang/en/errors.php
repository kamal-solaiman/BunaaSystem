<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Error Messages — English
|--------------------------------------------------------------------------
|
| The registry user messages from AI_DOCS/34_Error_Codes.md. Keys are the
| stable machine-readable error codes; only the message is translated
| (41_Internationalization_i18n.md §10, §22).
|
| A message must never reveal whether a record exists, whether an account
| exists, or any implementation detail (34_Error_Codes.md §2.8).
|
*/

return [

    // Authentication
    'AUTH_UNAUTHENTICATED' => 'Authentication is required.',
    'AUTH_SESSION_EXPIRED' => 'Your session has expired. Please log in again.',

    // Authorization
    'AUTHZ_UNAUTHORIZED' => 'You do not have permission to perform this action.',

    // Validation
    'VALIDATION_FAILED' => 'The submitted data is invalid.',

    // API contract / transport
    'API_MALFORMED_REQUEST' => 'The request could not be understood.',
    'API_UNSUPPORTED_ROUTE' => 'Not found.',
    'API_RATE_LIMIT_EXCEEDED' => 'Too many requests. Please try again later.',
    'RESOURCE_NOT_FOUND' => 'Not found.',

    // System
    'SYSTEM_UNEXPECTED' => 'An unexpected error occurred.',

];
