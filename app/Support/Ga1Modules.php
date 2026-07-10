<?php

namespace App\Support;

use App\Models\AtkCategory;
use App\Models\AtkItem;
use App\Models\AtkRequest;
use App\Models\AtkStockMovement;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\FacilityCategory;
use App\Models\PurchaseOrder;
use App\Models\ReorderAlert;
use App\Models\Survey;
use App\Models\SystemNotification;
use App\Models\TravelRealization;
use App\Models\TravelRequest;
use App\Models\Vendor;

class Ga1Modules
{
    public static function all(): array
    {
        return [
            'travel-requests' => [
                'title' => 'Business Travel Request',
                'model' => TravelRequest::class,
                'permission' => 'travel',
                'fields' => [
                    'destination' => 'required|string|max:255',
                    'purpose' => 'required|string',
                    'departure_date' => 'required|date',
                    'return_date' => 'required|date|after_or_equal:departure_date',
                    'estimated_cost' => 'required|numeric|min:0',
                    'attachment_path' => 'nullable|string|max:255',
                    'status' => 'required|string|in:draft,submitted,approved,rejected,revision,completed',
                ],
                'defaults' => ['status' => 'draft'],
                'owned' => true,
            ],
            'travel-realizations' => [
                'title' => 'Travel Realization',
                'model' => TravelRealization::class,
                'permission' => 'travel',
                'fields' => [
                    'travel_request_id' => 'required|exists:travel_requests,id',
                    'ticket_cost' => 'required|numeric|min:0',
                    'hotel_cost' => 'required|numeric|min:0',
                    'transport_cost' => 'required|numeric|min:0',
                    'daily_allowance' => 'required|numeric|min:0',
                    'other_cost' => 'required|numeric|min:0',
                    'evidence_path' => 'nullable|string|max:255',
                    'realized_at' => 'nullable|date',
                ],
            ],
            'facility-categories' => [
                'title' => 'Facility Categories',
                'model' => FacilityCategory::class,
                'permission' => 'facility',
                'fields' => ['name' => 'required|string|max:255|unique:facility_categories,name,{id}'],
            ],
            'facilities' => [
                'title' => 'Master Facility',
                'model' => Facility::class,
                'permission' => 'facility',
                'fields' => [
                    'facility_category_id' => 'required|exists:facility_categories,id',
                    'name' => 'required|string|max:255',
                    'code' => 'required|string|max:50|unique:facilities,code,{id}',
                    'capacity' => 'nullable|integer|min:1',
                    'description' => 'nullable|string',
                    'is_active' => 'boolean',
                ],
            ],
            'facility-bookings' => [
                'title' => 'Facility Booking',
                'model' => FacilityBooking::class,
                'permission' => 'facility',
                'fields' => [
                    'facility_id' => 'required|exists:facilities,id',
                    'title' => 'required|string|max:255',
                    'starts_at' => 'required|date',
                    'ends_at' => 'required|date|after:starts_at',
                    'status' => 'required|string|in:draft,submitted,approved,rejected,revision,completed',
                    'outlook_event_id' => 'nullable|string|max:255',
                ],
                'defaults' => ['status' => 'draft'],
                'owned' => true,
            ],
            'atk-categories' => [
                'title' => 'ATK Categories',
                'model' => AtkCategory::class,
                'permission' => 'atk',
                'fields' => ['name' => 'required|string|max:255|unique:atk_categories,name,{id}'],
            ],
            'atk-items' => [
                'title' => 'Master ATK',
                'model' => AtkItem::class,
                'permission' => 'atk',
                'fields' => [
                    'atk_category_id' => 'required|exists:atk_categories,id',
                    'code' => 'required|string|max:50|unique:atk_items,code,{id}',
                    'name' => 'required|string|max:255',
                    'photo_path' => 'nullable|string|max:255',
                    'stock' => 'required|integer|min:0',
                    'minimum_stock' => 'required|integer|min:0',
                ],
            ],
            'atk-requests' => [
                'title' => 'Request ATK',
                'model' => AtkRequest::class,
                'permission' => 'atk',
                'fields' => [
                    'department' => 'required|string|max:255',
                    'notes' => 'nullable|string',
                    'status' => 'required|string|in:draft,submitted,approved,rejected,revision,completed',
                ],
                'defaults' => ['status' => 'draft'],
                'owned' => true,
            ],
            'atk-stock-movements' => [
                'title' => 'Manajemen Stok',
                'model' => AtkStockMovement::class,
                'permission' => 'atk',
                'fields' => [
                    'atk_item_id' => 'required|exists:atk_items,id',
                    'type' => 'required|string|in:in,out,adjustment',
                    'quantity' => 'required|integer',
                    'notes' => 'nullable|string',
                ],
            ],
            'reorder-alerts' => [
                'title' => 'Reorder Alert',
                'model' => ReorderAlert::class,
                'permission' => 'atk',
                'fields' => [
                    'atk_item_id' => 'required|exists:atk_items,id',
                    'current_stock' => 'required|integer|min:0',
                    'minimum_stock' => 'required|integer|min:0',
                    'recommended_quantity' => 'required|integer|min:1',
                    'is_resolved' => 'boolean',
                ],
            ],
            'vendors' => [
                'title' => 'Vendor Management',
                'model' => Vendor::class,
                'permission' => 'po',
                'fields' => [
                    'name' => 'required|string|max:255|unique:vendors,name,{id}',
                    'pic' => 'required|string|max:255',
                    'email' => 'nullable|email|max:255',
                    'phone' => 'nullable|string|max:50',
                    'address' => 'nullable|string',
                    'is_active' => 'boolean',
                ],
            ],
            'purchase-orders' => [
                'title' => 'Purchase Order',
                'model' => PurchaseOrder::class,
                'permission' => 'po',
                'fields' => [
                    'vendor_id' => 'required|exists:vendors,id',
                    'po_number' => 'required|string|max:50|unique:purchase_orders,po_number,{id}',
                    'status' => 'required|string|in:draft,submitted,approved,ordered,completed',
                    'po_date' => 'required|date',
                    'total_amount' => 'required|numeric|min:0',
                ],
                'creator' => true,
            ],
            'surveys' => [
                'title' => 'Survey & CSAT',
                'model' => Survey::class,
                'permission' => 'csat',
                'fields' => [
                    'service_type' => 'required|string|max:255',
                    'status' => 'required|string|in:pending,sent,completed',
                    'sent_at' => 'nullable|date',
                    'completed_at' => 'nullable|date',
                ],
                'owned' => true,
            ],
            'notifications' => [
                'title' => 'Notification Center',
                'model' => SystemNotification::class,
                'permission' => 'notification',
                'fields' => [
                    'type' => 'required|string|max:255',
                    'channel' => 'required|string|in:in-app,email,teams',
                    'title' => 'required|string|max:255',
                    'body' => 'required|string',
                    'teams_webhook_url' => 'nullable|url|max:500',
                ],
                'owned' => true,
            ],
        ];
    }

    public static function get(string $slug): array
    {
        abort_unless(array_key_exists($slug, self::all()), 404);

        return self::all()[$slug] + ['slug' => $slug];
    }
}
