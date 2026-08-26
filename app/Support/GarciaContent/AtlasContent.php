<?php

namespace App\Support\GarciaContent;

final class AtlasContent
{
    /** @return array<int, array{industry: string, company_type: string, department: string, workflow: string, friction: string, friction_slug: string, solution_pattern: string}> */
    public static function examples(): array
    {
        return array_map(
            fn (array $example): array => array_combine(
                ['industry', 'company_type', 'department', 'workflow', 'friction', 'friction_slug', 'solution_pattern'],
                $example,
            ),
            [
                ['Public Health', 'Growing mid-market team', 'Clinical Operations', 'Public health intake follow-up', 'Customer intake bottlenecks', 'customer-intake-bottlenecks', 'Structured intake and routing'],
                ['Public Health', 'Multi-location operator', 'Compliance', 'Records reconciliation', 'Records reconciliation', 'records-reconciliation', 'Data cleanup workflow'],
                ['Education', 'Public agency', 'Compliance', 'Grant documentation', 'Knowledge silos', 'knowledge-silos', 'Shared knowledge base'],
                ['Education', 'Growing mid-market team', 'Student Services', 'Student services handoffs', 'Disconnected systems', 'disconnected-systems', 'Cross-system status layer'],
                ['Logistics', 'Multi-location operator', 'Operations', 'Inventory coordination', 'Inventory visibility', 'inventory-visibility', 'Operational dashboard'],
                ['Logistics', 'Regional service provider', 'Field Operations', 'Delivery exception review', 'Approval delays', 'approval-delays', 'Approval rules and exception queue'],
                ['Retail', 'Small business', 'Procurement', 'Supplier replenishment', 'Vendor coordination', 'vendor-coordination', 'Vendor coordination hub'],
                ['Retail', 'Multi-location operator', 'Customer Support', 'Return request intake', 'Duplicate work', 'duplicate-work', 'Structured intake and routing'],
                ['Restaurants', 'Multi-location operator', 'Operations', 'Location closeout review', 'Manual reporting', 'restaurant-closeout-reporting', 'Operational dashboard'],
                ['Hospitality & Tourism', 'Regional service provider', 'Customer Support', 'Guest request coordination', 'Disconnected systems', 'guest-request-disconnected-systems', 'Cross-system status layer'],
                ['Construction & Trades', 'Regional service provider', 'Field Operations', 'Field change approval', 'Approval delays', 'field-change-approval-delays', 'Approval rules and exception queue'],
                ['E-commerce', 'Growing mid-market team', 'Operations', 'Order exception review', 'Duplicate work', 'ecommerce-order-exceptions', 'Approval rules and exception queue'],
                ['Manufacturing', 'Enterprise division', 'Finance', 'Production reporting', 'Manual reporting', 'manual-reporting', 'Operational dashboard'],
                ['Manufacturing', 'Regional service provider', 'Operations', 'Quality issue tracking', 'Data quality', 'data-quality', 'Data cleanup workflow'],
                ['Government', 'Public agency', 'Customer Support', 'Permit request review', 'Legacy system dependency', 'legacy-system-dependency', 'System-of-record clarification'],
                ['Government', 'Public agency', 'Administration', 'Board packet preparation', 'Manual reporting', 'manual-reporting-government', 'Operational dashboard'],
                ['Professional Services', 'Professional practice', 'Sales', 'Client intake and qualification', 'Customer intake bottlenecks', 'customer-intake-bottlenecks-professional-services', 'Structured intake and routing'],
                ['Professional Services', 'Growing mid-market team', 'IT', 'Internal request triage', 'Disconnected systems', 'disconnected-systems-professional-services', 'Cross-system status layer'],
            ],
        );
    }
}
