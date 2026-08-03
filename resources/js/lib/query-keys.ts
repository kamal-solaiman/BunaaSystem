/**
 * Query key conventions.
 *
 * A query key must include every access-defining context — role scope, Teacher
 * Workspace, linked Student, Teacher relationship, resource identity, and list
 * criteria — so cached data can never be served across an access boundary
 * (AI_DOCS/12_Frontend_Architecture.md §8; 28_Coding_Standards.md §5.3).
 *
 * Scope is the first segment of every key, which makes an entire scope
 * invalidatable in one call when the authenticated context changes.
 */

/** Access scope that owns a cached record. */
export type QueryScope = 'platform' | 'teacher-workspace' | 'student' | 'parent' | 'auth';

/** Serializable criteria for a list query. */
export type QueryCriteria = Readonly<Record<string, string | number | boolean | null | undefined>>;

/**
 * Builds a scoped query key.
 *
 * @param scope    Access boundary the data belongs to.
 * @param resource Canonical resource name, e.g. 'educational-grades'.
 * @param context  Identifiers that define visibility, e.g. a linked Student id.
 */
export function queryKey(
    scope: QueryScope,
    resource: string,
    ...context: ReadonlyArray<string | number | QueryCriteria | undefined>
): ReadonlyArray<unknown> {
    return [scope, resource, ...context.filter((part) => part !== undefined)];
}

/** Key matching every cached entry inside one access scope. */
export function scopeKey(scope: QueryScope): ReadonlyArray<unknown> {
    return [scope];
}
