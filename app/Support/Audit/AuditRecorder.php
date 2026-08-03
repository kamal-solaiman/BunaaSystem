<?php

declare(strict_types=1);

namespace App\Support\Audit;

use App\Models\AuditLogEntry;
use Illuminate\Http\Request;

/**
 * Writes Audit Log entries.
 *
 * Every important action produces an entry (23_Security_Standards.md §15.2).
 * Entries capture actor, context, event, payload, and origin (§15.3), and are
 * append-only, immutable, and permanently retained (§15.4).
 *
 * This is a shared support boundary rather than a feature service, because the
 * Audit Log spans every feature. It records; it never authorizes and never
 * decides what is auditable — the caller does, from the documented event list.
 */
final class AuditRecorder
{
    /**
     * Record one audited event.
     *
     * @param  value-of<AuditEvent>|AuditEvent  $event
     * @param  array<string, mixed>|null  $details  Before/after snapshot (§15.3).
     */
    public function record(
        AuditEvent|string $event,
        string $affectedEntityName,
        ?int $affectedEntityId = null,
        ?int $actorUserId = null,
        ?string $actorRole = null,
        string $scopeContext = 'platform',
        ?int $teacherWorkspaceId = null,
        ?array $details = null,
        ?Request $request = null,
    ): AuditLogEntry {
        return AuditLogEntry::create([
            'actor_user_id' => $actorUserId,
            'actor_role' => $actorRole,
            'scope_context' => $scopeContext,
            'teacher_workspace_id' => $teacherWorkspaceId,
            'event_type' => $event instanceof AuditEvent ? $event->value : $event,
            'affected_entity_name' => $affectedEntityName,
            'affected_entity_id' => $affectedEntityId,
            'event_details' => $details,
            // Origin: IP address and device/client information (§15.3).
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'occurred_at' => now(),
        ]);
    }
}
