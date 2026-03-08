<?php
// lang/en/contact.php

return [
    'title' => 'Contact the Investigation Team',
    'subtitle' => 'Have information about a case? Want to collaborate? Reach out to us.',
    'confidential_stamp' => 'CONFIDENTIAL',
    
    'cards' => [
        'email' => [
            'title' => 'Email',
            'value' => 'contact@crimechronicles.com',
            'response' => 'Response within 24-48 hours'
        ],
        'tip_line' => [
            'title' => 'Tip Line',
            'value' => '1-800-CRIME-TIP',
            'response' => 'Anonymous tips accepted'
        ],
        'live_chat' => [
            'title' => 'Live Chat',
            'value' => 'Mon-Fri, 9AM-6PM EST',
            'response' => 'Click the chat icon below'
        ],
    ],
    
    'form' => [
        'title' => 'Send a Secure Message',
        'name' => 'Full Name',
        'email' => 'Email Address',
        'subject' => 'Subject',
        'message' => 'Message',
        'message_placeholder' => 'Please provide as much detail as possible about your case, including dates, locations, and any relevant information...',
        'anonymous' => 'Submit anonymously (no personal information stored)',
        'submit' => 'Send Secure Message',
        'subjects' => [
            'select' => 'Select a topic',
            'case_tip' => 'Case Tip / Information',
            'collaboration' => 'Collaboration Request',
            'press' => 'Press Inquiry',
            'other' => 'Other',
        ]
    ],
    
    'disclaimer' => '⚠️ For emergencies, please contact your local law enforcement immediately.',
    'encryption_note' => 'All messages are encrypted and handled confidentially.',
    
    'success_message' => 'Your message has been sent securely. We\'ll respond within 48 hours.',
    'error_message' => 'Please correct the errors below.',
];