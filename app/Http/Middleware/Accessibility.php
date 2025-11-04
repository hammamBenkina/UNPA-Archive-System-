<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Accessibility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessibility = [
            'admin' => [
                'applicants' => [],
                'applicants_requests' => [],
                'applicant_request_reason' => [],
                'branch' => [],
                'committee' => [],
                'committee_members' => [],
                'file' => [],
                'record' => [],
                'record_item' => [],
                'record_item_picture' => [],
                'role' => [],
                'users'  => ['create', 'edit', 'read', 'delete', 'export', 'report'],
                'user_history' => [],
            ],
            'data_entry' => [
                'applicants' => [],
                'applicants_requests' => [],
                'applicant_request_reason' => [],
                'branch' => [],
                'committee' => [],
                'committee_members' => [],
                'file' => [],
                'record' => ['create', 'edit', 'read', 'delete', 'export', 'report'],
                'record_item'   => ['create', 'edit', 'read', 'delete', 'export', 'report'],
                'record_item_picture'  => ['create', 'edit', 'read', 'delete', 'export', 'report'],
                'role' => [],
                'users' => [],
                'user_history' => [],
            ],
            'viewer' => [
                'applicants' => [],
                'applicants_requests' => [],
                'applicant_request_reason' => [],
                'branch' => [],
                'committee' => [],
                'committee_members' => [],
                'file' => [],
                'record' => ['read',  'report'],
                'record_item' => [],
                'record_item_picture' => [],
                'role' => [],
                'users' => [],
                'user_history' => [],
            ]
        ];
        return $next($request);
    }
}
