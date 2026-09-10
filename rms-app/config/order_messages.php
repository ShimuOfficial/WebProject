<?php

return [
    'validation' => [
        'table_required' => 'Please select a table from the list before submitting the order.',
        'menu_required' => 'Please select at least one menu item before submitting the order.',
        'cash_paid_amount' => 'Paid amount must be at least the total amount.',
        'payment_reference_required' => 'Please enter the payment reference (Txn ID).',
        'inventory_shortage' => 'Some selected items are out of stock. Please reduce the quantity.',
    ],

    'controller' => [
        'paid_amount_min' => 'Negative value not accepted. Paid amount must be a positive number.',
        'paid_amount_required' => 'Please enter the received cash amount.',
        'payment_reference_required' => 'Reference is required for bKash, Rocket, and card payments.',
        'cash_paid_less_than_total' => 'Paid amount must be at least the total amount.',
        'paid_order_success' => 'Paid on-site order created successfully.',
        'reservation_warning' => 'Warning: reservation exceeds available stock for: :items',
    ],

    'exceptions' => [
        'session_expired_json' => 'Your session has expired. Please refresh and try again.',
        'session_expired_login' => 'Your session expired. Please sign in again.',
        'session_expired_back' => 'Your session expired. Please try again.',
        'unauthenticated' => 'Unauthenticated.',
        'signin_first' => 'Please sign in first.',
        'forbidden' => 'You are not allowed to perform this action.',
        'not_found' => 'The requested record or page was not found.',
        'method_not_allowed' => 'This request method is not allowed.',
        'too_many_requests' => 'Too many requests. Please try again later.',
    ],

    'ui' => [
        'table_placeholder' => 'Select a table from the list',
        'menu_empty' => 'No recipe configured for selected items.',
        'alert_autohide_ms' => 3000,
    ],
];
