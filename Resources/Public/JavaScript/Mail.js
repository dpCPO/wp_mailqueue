define([
  'jquery',
  'TYPO3/CMS/Backend/Modal',
  'TYPO3/CMS/Backend/Severity',
  'TYPO3/CMS/WpMailqueue/DataTables'
], function ($, Modal, Severity) {
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

  const maillogTable = $('#maillog-table');
  const table = maillogTable.DataTable({
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
      {data: 'actions'},
    ],
    order: [6, 'desc'],
    language: language,
    serverSide: true,
    orderable: false
  });

  $('body').on('click', '.js-delete-mail', function (e) {
    e.preventDefault();
    Modal.confirm(
        'E-Mail löschen',
        'Soll diese E-Mail wirklich gelöscht werden?',
        Severity.warning,
        [
          {
            text: 'Schließen',
            btnClass: 'btn-default',
            trigger: function () {
              Modal.dismiss();
            }
          },
          {
            text: 'OK',
            btnClass: 'btn-warning',
            trigger: function () {
              window.location = e.currentTarget.dataset.href;
              Modal.dismiss();
            }
          }
        ]
    )
  });
});
