<?php

namespace WEBprofil\WpMailqueue\Controller;

use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use Psr\Http\Message\ResponseInterface;
use Deployer\Host\Storage;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\PathUtility;

class BackendController extends ActionController
{
    protected static $table = 'tx_wpmailqueue_domain_model_mail';
    private ModuleTemplateFactory $moduleTemplateFactory;
    public function __construct(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function listAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/WpMailqueue/DataTables');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/WpMailqueue/Mail');
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function deleteAction(ServerRequestInterface $request): ResponseInterface
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
            ->fetchOne();

        $jsonMails = [];

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $iconMarkup = GeneralUtility::makeInstance(IconFactory::class)->getIcon('actions-delete', Icon::SIZE_SMALL)->render();
        $result = $this->buildQuery($params);
        while ($mail = $result->fetchAssociative()) {
            $jsonMail = $mail;
            $jsonMail['date_sent'] = $jsonMail['date_sent'] ? date('d.m.Y H:i', $jsonMail['date_sent']) : 'In der Warteschlange';
            $url = $uriBuilder->buildUriFromRoutePath('/delete', ['uid' => $mail['uid']]);
            $jsonMail['actions'] = '<a class="js-delete-mail btn btn-default" data-href="' . $url . '" title="Mail löschen">' . $iconMarkup . '</a>';

            $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
            $storages = $storageRepository->findAll();

            $mailAttachements = [];
            $attachements = [];
            if( !empty($jsonMail['attachements']) ){
            	$attachements = explode(',', $jsonMail['attachements']);
            }
            foreach ($attachements as $attachement) {
                $fileName = $attachement;
                $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

                $publicPath = Environment::getPublicPath() . "/";

                // cleanup filepaths to find file object
                /** @var ResourceStorage $storage */
                foreach ($storages as $storage) {
                    $storageRecord = $storage->getStorageRecord();
                    $configuration = $storage->getConfiguration();

                    $basePath = $configuration['basePath'];
                    if( $configuration['pathType'] == 'relative' ){
                    	$basePath = PathUtility::getCanonicalPath( $publicPath . $configuration['basePath'] );
                    }
                    $fileName = str_replace( $basePath, $storageRecord['uid'].':', $fileName);
                }

                // search file object
                $file = null;
                try {
                    $file = $resourceFactory->getFileObjectFromCombinedIdentifier($fileName);
                } catch (\InvalidArgumentException $e) {                }

                $fileData = [
                    'name' => $attachement
                ];

                if ($file) {
                    $fileData['url'] = $file->getPublicUrl();
                }
                $mailAttachements[] = $fileData;
            }

            $attachementsHtml = [];
            foreach ($mailAttachements as $attachement) {
                if ($attachement['url']) {
                    $attachementsHtml[] = '<a href="' . $attachement['url'] . '" target="_blank">' . $attachement['name'] . '</a>';
                } else {
                    $attachementsHtml[] = '<span class="bg-danger" title="File is missing in filesystem">' . $attachement['name'] . '</span>';
                }
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
            return $queryBuilder->execute()->fetchOne();
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

