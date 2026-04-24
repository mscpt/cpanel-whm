<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // contracts: renewal dashboard query hits status + renewal_date
        Schema::table('contracts', function (Blueprint $table) {
            $table->index(['status', 'renewal_date'], 'contracts_status_renewal_date_idx');
        });

        // leads: WHMCS import deduplicates by email; order used in kanban sorting
        Schema::table('leads', function (Blueprint $table) {
            $table->index('email', 'leads_email_idx');
            $table->index(['pipeline_stage_id', 'order'], 'leads_stage_order_idx');
        });

        // proposals: status filter on index page
        Schema::table('proposals', function (Blueprint $table) {
            $table->index('status', 'proposals_status_idx');
            $table->index('created_by', 'proposals_created_by_idx');
        });

        // track_events: stats queries filter by created_at; polymorphic lookup
        Schema::table('track_events', function (Blueprint $table) {
            $table->index('created_at', 'track_events_created_at_idx');
            $table->index(['trackable_type', 'trackable_id'], 'track_events_trackable_idx');
        });

        // activities: polymorphic lookup on client/lead timeline
        Schema::table('activities', function (Blueprint $table) {
            $table->index(['activityable_type', 'activityable_id'], 'activities_activityable_idx');
        });

        // audit_logs: sorted by created_at descending; filtered by action and user_id
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('created_at', 'audit_logs_created_at_idx');
            $table->index(['action', 'user_id'], 'audit_logs_action_user_idx');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', fn(Blueprint $t) => $t->dropIndex('contracts_status_renewal_date_idx'));
        Schema::table('leads', function (Blueprint $t) {
            $t->dropIndex('leads_email_idx');
            $t->dropIndex('leads_stage_order_idx');
        });
        Schema::table('proposals', function (Blueprint $t) {
            $t->dropIndex('proposals_status_idx');
            $t->dropIndex('proposals_created_by_idx');
        });
        Schema::table('track_events', function (Blueprint $t) {
            $t->dropIndex('track_events_created_at_idx');
            $t->dropIndex('track_events_trackable_idx');
        });
        Schema::table('activities', fn(Blueprint $t) => $t->dropIndex('activities_activityable_idx'));
        Schema::table('audit_logs', function (Blueprint $t) {
            $t->dropIndex('audit_logs_created_at_idx');
            $t->dropIndex('audit_logs_action_user_idx');
        });
    }
};
