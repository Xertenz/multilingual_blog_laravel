<?php
// lang/en/privacy.php

return [
    'title' => 'Privacy Policy',
    'subtitle' => 'How we protect your information',
    'last_updated' => 'Last updated: :date',
    'confidential_stamp' => 'CONFIDENTIAL',
    
    'sections' => [
        'information_collect' => [
            'title' => 'Information We Collect',
            'content' => 'As investigators of truth, we collect only the information necessary to provide our services:',
            'items' => [
                [
                    'label' => 'Personal Information:',
                    'text' => 'Name and email address when you subscribe or comment.'
                ],
                [
                    'label' => 'Case Data:',
                    'text' => 'Information you voluntarily share in investigation notes.'
                ],
                [
                    'label' => 'Technical Data:',
                    'text' => 'IP address, browser type, and device information for security.'
                ],
            ]
        ],
        'how_we_use' => [
            'title' => 'How We Use Your Information',
            'content' => 'Your information helps us solve the mystery of what content you prefer:',
            'items' => [
                'To provide and maintain our service',
                'To notify you about new cases and developments',
                'To improve our investigation techniques',
                'To protect against fraudulent activity',
            ]
        ],
        'data_protection' => [
            'title' => 'Data Protection',
            'content' => 'We employ state-of-the-art security measures worthy of the most sensitive investigations:',
            'items' => [
                '256-bit encryption for all data transmission',
                'Regular security audits',
                'Strict access controls',
                'Anonymous data for research purposes',
            ]
        ],
        'cookies' => [
            'title' => 'Cookies',
            'content' => 'Like clues at a crime scene, cookies help us understand your journey:',
            'description' => 'We use essential cookies for site functionality and optional analytics cookies to improve our content. You can disable cookies in your browser settings, but some features may not work properly.',
        ],
        'third_party' => [
            'title' => 'Third-Party Links',
            'content' => 'Our case files may contain links to external evidence. We are not responsible for the privacy practices of these external sites.',
        ],
        'your_rights' => [
            'title' => 'Your Rights',
            'content' => 'You have the right to:',
            'items' => [
                'Access your personal data',
                'Correct inaccurate information',
                'Request deletion of your data',
                'Opt-out of communications',
            ]
        ],
        'contact' => [
            'title' => 'Contact Us',
            'content' => 'For privacy-related inquiries, contact our Data Protection Officer:',
            'email' => 'Email:',# :email',
            'response_time' => 'Response Time:',# Within 48 hours',
        ]
    ],
];