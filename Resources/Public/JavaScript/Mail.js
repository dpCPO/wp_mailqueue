// PageRenderer sichert ab, dass jquery und datatables.js vorher geladen wurde
// import $ from 'jquery';
// import '@webprofil/wp-mailqueue/datatables.js';

import Modal from '@typo3/backend/modal.js';
import Severity from '@typo3/backend/severity.js';
import DocumentService from '@typo3/core/document-service.js';

const language = {
    "sEmptyTable": "Keine Daten in der Tabelle vorhanden",
    "sInfo": "_START_ bis _END_ von _TOTAL_ Einträgen",
    "sInfoEmpty": "0 bis 0 von 0 Einträgen",
    "sInfoFiltered": "(gefiltert von _MAX_ Einträgen)",
    "sInfoPostFix": "",
    "sInfoThousands": ".",
    "sLengthMenu": "_MENU_ Einträge anzeigen",
    "sLoadingRecords": "Wird geladen...",
    "sProcessing": "Bitte warten...",
    "sSearch": "Suchen",
    "sZeroRecords": "Keine Einträge vorhanden.",
    "oPaginate": {
        "sFirst": "Erste",
        "sPrevious": "Zurück",
        "sNext": "Nächste",
        "sLast": "Letzte"
    },
    "oAria": {
        "sSortAscending": ": aktivieren, um Spalte aufsteigend zu sortieren",
        "sSortDescending": ": aktivieren, um Spalte absteigend zu sortieren"
    }
};


DocumentService.ready().then( () => {

    const maillogTable = $('#maillog-table');

    if (maillogTable.length) {
        $(maillogTable).DataTable({
            ajax: TYPO3.settings.ajaxUrls.wp_mails,
            pageLength: 10,
            columns: [
                {data: 'sender', orderable: false},
                {data: 'recipient', orderable: false},
                {data: 'cc', orderable: false},
                {data: 'bcc', orderable: false},
                {data: 'subject', orderable: false},
                {data: 'attachements', orderable: false},
                {data: 'crdate'},
                {data: 'date_sent', orderable: false},
                {data: 'actions'}
            ],
            order: [[6, 'desc']],
            language: language,
            serverSide: true
        });
    }

    document.body.addEventListener('click', function (e) {
        if (e.target.closest('.js-delete-mail')) {
            e.preventDefault();
            Modal.confirm(
                'E-Mail löschen',
                'Soll diese E-Mail wirklich gelöscht werden?',
                Severity.warning,
                [
                    {
                        text: 'Schließen',
                        btnClass: 'btn-default',
                        trigger: () => Modal.dismiss()
                    },
                    {
                        text: 'OK',
                        btnClass: 'btn-warning',
                        trigger: () => {
                            window.location = e.target.closest('.js-delete-mail').dataset.href;
                            Modal.dismiss();
                        }
                    }
                ]
            );
        }
    });
});