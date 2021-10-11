<?php

namespace WEBprofil\WpMailqueue\Controller;

use Bitmotion\SecureDownloads\Factory\SecureLinkFactory;
use Bitmotion\SecureDownloads\Service\SecureDownloadService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class BackendController extends ActionController
{
    /**
     * Backend Template Container
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    protected static $table = 'tx_wpmailqueue_domain_model_mail';

    public function listAction()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/WpMailqueue/DataTables');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/WpMailqueue/Mail');
    }

    public function deleteAction(ServerRequestInterface $request): Response
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->update(self::$table)
            ->set('deleted', 1)
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($request->getQueryParams()['uid'], \PDO::PARAM_INT)))
            ->execute();

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $url = $uriBuilder->buildUriFromRoute('web_WpMailqueueMaillist');
        return new RedirectResponse($url);
    }

    public function getMailsAsJson(ServerRequestInterface $request): Response
    {
        $params = $request->getQueryParams();

        $queryBuilder = $this->getQueryBuilder();
        $count = $queryBuilder
            ->count('uid')
            ->from(self::$table)
            ->execute()
            ->fetchColumn();

        $jsonMails = [];

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $iconMarkup = GeneralUtility::makeInstance(IconFactory::class)->getIcon('actions-delete', Icon::SIZE_SMALL)->render();
        $result = $this->buildQuery($params);
        while ($mail = $result->fetch()) {
            $jsonMail = $mail;
            $jsonMail['date_sent'] = $jsonMail['date_sent'] ? date('d.m.Y H:i', $jsonMail['date_sent']) : 'In der Warteschlange';
            $url = $uriBuilder->buildUriFromRoutePath('/delete', ['uid' => $mail['uid']]);
            $jsonMail['actions'] = '<a class="js-delete-mail btn btn-default" data-href="' . $url . '" title="Mail löschen">' . $iconMarkup . '</a>';

            $securedAttachements = [];
            $secureDownloadService = GeneralUtility::makeInstance(SecureDownloadService::class);
            $attachements = explode(',', $jsonMail['attachements']);
            foreach ($attachements as $attachement) {
                if ($secureDownloadService->pathShouldBeSecured($attachement)) {
                    $securedUrl = GeneralUtility::makeInstance(SecureLinkFactory::class, $attachement);
                    $securedAttachements[] = [
                        'name' => $attachement,
                        'url' => $securedUrl->getUrl(),
                    ];
                } else {
                    $securedAttachements[] = [
                        'name' => $attachement,
                        'url' => $attachement
                    ];
                }
            }

            $attachementsHtml = [];
            foreach ($securedAttachements as $attachement) {
                $attachementsHtml[] = '<a href="/' . $attachement['url'] . '" target="_blank">' . $attachement['name'] . '</a>';
            }

            $jsonMail['attachements'] = implode(', ', $attachementsHtml);
            $jsonMail['crdate'] = date('d.m.Y H:i', $jsonMail['crdate']);
            $jsonMails[] = $jsonMail;
        }

        $response = [
            'draw' => $params['draw'],
            'start' => $params['start'],
            'length' => $params['length'],
            'recordsTotal' => $count,
            'recordsFiltered' => $this->buildQuery($params, true),
            'order' => $params['order'],
            'search' => $params['search'],
            'columns' => $params['columns'],
            'data' => $jsonMails
        ];

        return new JsonResponse($response);
    }

    protected function buildQuery($params, $returnCount = false)
    {
        $queryBuilder = $this->getQueryBuilder();

        if ($returnCount) {
            $queryBuilder
                ->count('uid')
                ->from('tx_wpmailqueue_domain_model_mail');
        } else {
            $queryBuilder
                ->select('uid', 'sender', 'recipient', 'cc', 'bcc', 'subject', 'attachements', 'date_sent', 'crdate')
                ->from('tx_wpmailqueue_domain_model_mail')
                ->orderBy('crdate', 'DESC')
                ->setMaxResults((int)$params['length'])
                ->setFirstResult((int)$params['start']);
        }

        if ($params['search']['value'] !== '' && strlen($params['search']['value']) > 2) {
            $queryBuilder
                ->where(
                    $queryBuilder->expr()
                        ->like(
                            'subject',
                            $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards($params['search']['value']) . '%')
                        )
                )
                ->orWhere(
                    $queryBuilder->expr()
                        ->like(
                            'recipient',
                            $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards($params['search']['value']) . '%')
                        )
                );
        }

        if ($returnCount) {
            return $queryBuilder->execute()->fetchColumn();
        } else {
            return $queryBuilder->execute();
        }
    }

    protected function getQueryBuilder()
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::$table);
    }
}
