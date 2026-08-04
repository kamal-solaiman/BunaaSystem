/**
 * Shared API contracts.
 *
 * These types mirror the response envelopes defined in
 * AI_DOCS/10_API_Design.md §6, §7, §10. They describe the transport shape only —
 * no feature or domain model is declared here.
 */

/** Successful response envelope. */
export interface ApiSuccess<TData> {
    success: true;
    data: TData;
    meta?: PaginationMeta;
}

/** Pagination metadata returned by list endpoints. */
export interface PaginationMeta {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
}

/** Paginated response envelope. */
export interface ApiPaginated<TItem> {
    success: true;
    data: TItem[];
    meta: PaginationMeta;
}

/** Error response envelope. */
export interface ApiErrorEnvelope {
    success: false;
    error: {
        code: string;
        message: string;
        details?: Record<string, unknown>;
    };
    request_id?: string;
    /** Present on 422 responses only: field name to messages. */
    errors?: Record<string, string[]>;
}

/**
 * Stable frontend error taxonomy.
 *
 * Every HTTP outcome is mapped to one of these classes at the HTTP boundary so
 * the UI reacts to a small, predictable set of cases
 * (34_Error_Codes.md §26.2; 28_Coding_Standards.md §5.8).
 */
export type ApiErrorKind =
    | 'unauthenticated'
    | 'unauthorized'
    | 'not-found'
    | 'conflict'
    | 'validation'
    | 'rate-limited'
    | 'server'
    | 'network'
    | 'cancelled';

/** Standard list query inputs (10_API_Design.md §7–§9). */
export interface ListQuery {
    page?: number;
    per_page?: number;
    /** A leading minus sign requests descending order. */
    sort?: string;
    [filter: string]: string | number | boolean | undefined;
}
