<?php

use Hphio\Auth\Authenticator;
use Hphio\Auth\Login;
use Hphio\Auth\Nonce\NonceService;
use Hphio\Auth\UserNav;
use Hphio\Auth\Users\AccountRecoveryService;
use Hphio\Auth\Users\UserActivationService;
use League\Container\Container;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Billings\DeleteBillingService;
use Supp\Api\Billings\Export\ExportBillingService;
use Supp\Api\Billings\Export\GetAllBillingsExportService;
use Supp\Api\Billings\NewBillingService;
use Supp\Api\Billings\UpdateBillingService;
use Supp\Api\Clients\ClientService;
use Supp\Api\Clients\NewCompanyService;
use Supp\Api\Comments\NewUploadCommentService;
use Supp\Api\Faqs\FaqService;
use Supp\Api\Projects\DeleteProjectsService;
use Supp\Api\Projects\GetProjectsService;
use Supp\Api\Projects\NewProjectService;
use Supp\Api\Projects\ProjectsBillingService;
use Supp\Api\Signup\SignupService;
use Supp\Api\Auth\Users\PasswordResetService;
use Supp\Api\Statuses\StatusService;
use Supp\Api\TicketCategories\GetTicketCategories;
use Supp\Api\Tickets\AssignTicketService;
use Supp\Api\Tickets\GetAllTicketsService;
use Supp\Api\Tickets\GetSingleTicketService;
use Supp\Api\Tickets\GetTicketsByCategoryService;
use Supp\Api\Tickets\GetTicketsByStatusService;
use Supp\Api\Tickets\NewTicketCommentService;
use Supp\Api\Tickets\NewTicketService;
use Supp\Api\Tickets\NewUploadTicketService;
use Supp\Api\Tickets\TicketCommentsService;
use Supp\Api\Tickets\TicketsReportService;
use Supp\Api\Tickets\UpdateTicketStatusService;
use Supp\Api\Users\GetUserMyService;
use Supp\Api\Users\GetUsersService;
use Supp\Api\Users\UserBillingsService;

function getRouter(Container $container, ServerRequestInterface $request): Router
{
    // $strategy = (new ApplicationStrategy)->setContainer($container);
    // $strategy = $container->get(ApplicationStrategy::class);
    $strategy = $container->get(JsonStrategy::class); //Strategy changed by Michael Munger <mj@hph.io> on 2022-09-12 @ 10:10am to allow the router to auto-generate OPTIONS responses for monitoring.
    $strategy->setContainer($container);

    // $router = (new Router)->setStrategy($strategy);
    $router = $container->get(Router::class);

    if ($request->getMethod() == 'OPTIONS') {
        $strategy->addResponseDecorator(function (ResponseInterface $response): ResponseInterface {
            return $response->withAddedHeader('Access-Control-Allow-Headers', 'Content-Type, authorization, x-mock-match-request-body');
        });
    }

    $router->setStrategy($strategy);

    $router->map('POST', '/api/v1/auth/login', Login::class);
    $router->map('POST', '/api/v1/auth/users/activate/{uuid:alphanum_dash}', UserActivationService::class);
    $router->map('POST', '/api/v1/auth/users/recover/{uuid:alphanum_dash}', AccountRecoveryService::class);
    $router->map('POST', '/api/v1/auth/users/reset', PasswordResetService::class);
    $router->map('GET', '/api/v1/auth/nonce/{uuid:alphanum_dash}', NonceService::class);
    $router->map('POST', '/api/v1/signup', SignupService::class);


    $router->group('/api/v1', function ($router) use ($container) {

        $router->map('GET', '/auth/users/{id:number}/nav', UserNav::class);
        $router->map('POST', '/clients', NewCompanyService::class);
        $router->map('GET', '/clients', ClientService::class);
        $router->map('POST', '/projects', NewProjectService::class);
        $router->map('GET', '/projects', GetProjectsService::class);
        $router->map('DELETE', '/projects/{id:number}', DeleteProjectsService::class);
        $router->map('GET', '/projects/billings', ProjectsBillingService::class);

        $router->map('GET', '/users/my', GetUserMyService::class);
        $router->map('GET', '/users', GetUsersService::class);

        $router->map('GET', '/users/{id:number}/billing', UserBillingsService::class);
        $router->map('POST', '/billings', NewBillingService::class);
        $router->map('PATCH', '/billings/{id:number}', UpdateBillingService::class);
        $router->map('DELETE', '/billings/{id:number}', DeleteBillingService::class);

        $router->map('GET', '/billings/export/{id:number}', ExportBillingService::class);
        $router->map('GET', '/billings/export/types', GetAllBillingsExportService::class);

        $router->map('GET', '/tickets/status/{filter:number}', GetTicketsByStatusService::class);
        $router->map('GET', '/tickets/categories/{filter:number}', GetTicketsByCategoryService::class);


        $router->map('GET', '/ticket-categories', GetTicketCategories::class);
        $router->map('GET', '/tickets/reports', TicketsReportService::class);

        $router->map('GET', '/tickets/{id:number}/comments', TicketCommentsService::class);
        $router->map('GET', '/tickets', GetAllTicketsService::class);
        $router->map('GET', '/tickets/{id:number}/single', GetSingleTicketService::class);

        $router->map('POST', '/tickets/{filter:number}', NewTicketService::class);
        $router->map('POST', '/tickets/{id:number}/comments/{filter:word}', NewTicketCommentService::class);
        $router->map('POST', '/tickets/{id:number}/uploads', NewUploadTicketService::class);
        $router->map('POST', '/comments/{id:number}/uploads', NewUploadCommentService::class);

        $router->map('PATCH', '/tickets/{id:number}/status', UpdateTicketStatusService::class);
        $router->map('PATCH', '/tickets/{id:number}/assign', AssignTicketService::class);

        $router->map('GET', '/faqs', FaqService::class);
        $router->map('GET', '/statuses', StatusService::class);


    })->middleware(new Authenticator($container));

    return $router;
}
