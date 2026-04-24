<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\TrackEvent;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TrackController extends Controller
{
    // 1x1 transparent GIF
    private const GIF = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    public function pixel(Request $request, string $token)
    {
        // Rate limit: 10 hits per IP per minute
        $key = 'track_' . md5($request->ip() . $token);
        if (cache()->get($key, 0) > 10) {
            return $this->gif();
        }
        cache()->put($key, cache()->get($key, 0) + 1, 60);

        // Try to resolve trackable
        $proposal = Proposal::where('token', $token)->first();

        TrackEvent::create([
            'token'          => $token,
            'trackable_type' => $proposal ? Proposal::class : null,
            'trackable_id'   => $proposal?->id,
            'event'          => 'open',
            'context'        => $request->query('type', 'pixel'),
            'ip'             => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'created_at'     => now(),
        ]);

        if ($proposal && $proposal->status === 'sent') {
            $proposal->update(['status' => 'viewed', 'viewed_at' => $proposal->viewed_at ?? now()]);
        }

        return $this->gif();
    }

    private function gif()
    {
        return response(base64_decode(self::GIF), 200, [
            'Content-Type'  => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma'        => 'no-cache',
        ]);
    }
}
