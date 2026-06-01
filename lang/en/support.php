<?php

return [
    'nav' => [
        'contact' => 'Contact Us',
        'help' => 'Help / FAQ',
        'how' => 'How It Works',
        'terms' => 'Terms & Conditions',
        'privacy' => 'Privacy Policy',
    ],
    'common' => [
        'back_home' => 'Back to home',
        'support' => 'Support',
    ],
    'contact' => [
        'title' => 'Contact Us',
        'subtitle' => 'Need help with a booking, public match, or field owner account? Use these support details to reach the MatchPoint team.',
        'channels' => [
            ['label' => 'Email', 'value' => 'support@matchpoint.com', 'description' => 'Best for account access, booking issues, and support questions that need a written record.'],
            ['label' => 'Phone', 'value' => '+62 812 1727 5362', 'description' => 'Use this for urgent booking questions during support hours.'],
            ['label' => 'Location', 'value' => 'Malang, Indonesia', 'description' => 'MatchPoint currently supports sports venue booking around Malang and nearby areas.'],
        ],
        'before_contact_title' => 'Before contacting support',
        'before_contact' => [
            ['title' => 'Booking problems', 'body' => 'Prepare your booking date, venue name, selected time slot, and current booking status.'],
            ['title' => 'Field owner questions', 'body' => 'Include your account email and the field name so the admin can review your approval or field visibility issue.'],
        ],
    ],
    'help' => [
        'title' => 'Help / FAQ',
        'subtitle' => 'Quick answers for visitors, players, and field owners using MatchPoint.',
        'faq_title' => 'Frequently Asked Questions',
        'faqs' => [
            ['question' => 'Do I need an account to browse fields?', 'answer' => 'No. Visitors can browse approved fields and public matches, but booking a field or joining a match requires login.'],
            ['question' => 'How is a field booking confirmed?', 'answer' => 'After selecting a field and time slot, the booking enters the review flow. The field owner checks the booking details and confirms or rejects it.'],
            ['question' => 'Why is my booking still pending?', 'answer' => 'A booking remains pending until the field owner reviews and confirms it.'],
            ['question' => 'Can I reschedule a booking?', 'answer' => 'Only confirmed outdoor bookings can be rescheduled, and the request must be made before the booking date. Indoor bookings cannot be rescheduled.'],
            ['question' => 'How do public matches work?', 'answer' => 'A user with a confirmed booking can create a public match, set teams and participant details, and allow other users to request a spot.'],
            ['question' => 'Who approves field owner accounts?', 'answer' => 'Admins review field owner registrations. A field owner must be active before their fields can appear publicly.'],
        ],
        'need_help_title' => 'Still need help?',
        'need_help_body' => 'The Contact page is available to everyone, even when you are not logged in.',
    ],
    'how' => [
        'title' => 'How It Works',
        'subtitle' => 'A simple overview of the main MatchPoint flow from browsing a field to joining a public match.',
        'steps' => [
            ['eyebrow' => 'Step 1', 'title' => 'Browse fields', 'body' => 'Search approved venues by sport, location, availability, and price before choosing a field detail page.'],
            ['eyebrow' => 'Step 2', 'title' => 'Book a time slot', 'body' => 'Select an available date and time slot, then review the booking summary before submitting the booking.'],
            ['eyebrow' => 'Step 3', 'title' => 'Wait for confirmation', 'body' => 'The field owner reviews your booking. Confirmed bookings become active and appear in your dashboard.'],
            ['eyebrow' => 'Step 4', 'title' => 'Create or join matches', 'body' => 'Confirmed bookings can become public matches. Other players can join by choosing a team and submitting a join request.'],
        ],
    ],
    'terms' => [
        'title' => 'Terms & Conditions',
        'subtitle' => 'These terms explain the basic responsibilities for using MatchPoint booking and public match features.',
        'sections' => [
            ['title' => 'Account responsibility', 'body' => 'Users must provide accurate information and keep account credentials secure. Field owners are responsible for keeping field details, pricing, and schedules accurate.'],
            ['title' => 'Manual payments', 'body' => 'MatchPoint supports manual payment review outside the system. Users, field owners, and admins are responsible for following the agreed payment instructions.'],
            ['title' => 'Booking status', 'body' => 'Bookings may be pending, confirmed, completed, or cancelled. Confirmed bookings are automatically completed after the scheduled date and time has passed when the scheduler runs.'],
            ['title' => 'Reschedule and cancellation', 'body' => 'Only confirmed outdoor bookings may be rescheduled before the booking date. Pending bookings may be cancelled according to the platform rules. Refund handling is outside MatchPoint.'],
            ['title' => 'Public matches', 'body' => 'Match creators are responsible for match details, team setup, and participant approval.'],
        ],
    ],
    'privacy' => [
        'title' => 'Privacy Policy',
        'subtitle' => 'This page explains what data MatchPoint uses to provide booking, notification, and public match features.',
        'sections' => [
            ['title' => 'Information we collect', 'body' => 'MatchPoint stores account details, booking records, selected time slots, notifications, and public match participation records.'],
            ['title' => 'How data is used', 'body' => 'Data is used to manage bookings, notify users and field owners, approve accounts, and show public match availability.'],
            ['title' => 'Who can see booking information', 'body' => 'Booking information is visible only to users with the right role, such as the booking user, the related field owner, or an admin.'],
            ['title' => 'Notifications and audit logs', 'body' => 'The system stores notifications and audit logs so important booking, payment, account, and match events can be reviewed.'],
            ['title' => 'Data protection', 'body' => 'Users should avoid submitting unrelated personal information. MatchPoint keeps account and activity records only for platform operation and review.'],
        ],
    ],
];
