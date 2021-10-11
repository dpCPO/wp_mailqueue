<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail',
        'label' => 'subject',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
        ],
        'searchFields' => 'subject,body,sender,cc,bcc,attachements,date_sent,type',
        'iconfile' => 'EXT:wp_mailqueue/Resources/Public/Icons/tx_wpmailqueue_domain_model_mail.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'subject, body, sender, cc, bcc, attachements, date_sent, type'],
    ],
    'columns' => [

        'subject' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'body' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.body',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'bodyHtml' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.bodyHtml',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'sender' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.sender',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'recipient' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.recipient',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'cc' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.cc',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'bcc' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.bcc',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'attachements' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.attachements',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'date_sent' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.date_sent',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'int,datetime'
            ],
        ],
        'type' => [
            'exclude' => false,
            'label' => 'LLL:EXT:wp_mailqueue/Resources/Private/Language/locallang_db.xlf:tx_wpmailqueue_domain_model_mail.type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

    ],
];
