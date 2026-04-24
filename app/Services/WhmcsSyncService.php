<?php

namespace App\Services;

/**
 * Stub for future WHMCS API synchronization (Phase 2).
 *
 * When implemented, this service will:
 * - Connect to the WHMCS API using credentials from settings
 * - Fetch clients, services, and invoices
 * - Sync them to the CRM leads/clients/contracts tables
 * - Run on a scheduled task every X hours via App\Console\Kernel
 */
class WhmcsSyncService
{
    public function sync(): void
    {
        // TODO Phase 2: implement WHMCS API sync
        throw new \RuntimeException('WHMCS API sync not yet implemented. Use CSV import for now.');
    }
}
