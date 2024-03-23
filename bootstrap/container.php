<?php


use Hphio\Auth\ApiService;
use Hphio\Auth\Login;
use Hphio\Auth\UserNav;
use Hphio\Auth\Users\AccountRecoveryService;
use Hphio\Auth\Users\UserActivationService;
use Hphio\Auth\Models\User;
use Hphio\Auth\Models\UserToken;
use Hphio\Auth\Models\UserTokenBase;
use Supp\Api\Auth\Users\PasswordResetService;
use hphio\util\RandomGenerator;
use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\ResponseFactory;
use League\Container\Container;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\JsonStrategy;
use Supp\Api\Billings\AdminDeleteBilling;
use Supp\Api\Billings\AdminNewBilling;
use Supp\Api\Billings\AdminUpdateBilling;
use Supp\Api\Billings\DeleteBilingServiceFactory;
use Supp\Api\Billings\DeleteBillingService;
use Supp\Api\Billings\Export\Last12months;
use Supp\Api\Billings\Export\Last30days;
use Supp\Api\Billings\Export\Last90days;
use Supp\Api\Billings\Export\LastMonth;
use Supp\Api\Billings\Export\LastWeek;
use Supp\Api\Billings\Export\LastYear;
use Supp\Api\Billings\Export\ExportBillingService;
use Supp\Api\Billings\Export\ExportBillingServiceFactory;
use Supp\Api\Billings\Export\ThisMonth;
use Supp\Api\Billings\Export\ThisWeek;
use Supp\Api\Billings\Export\ThisYear;
use Supp\Api\Billings\Export\Today;
use Supp\Api\Billings\Export\Yesterday;
use Supp\Api\Billings\Export\GetAllBillingsExportService;
use Supp\Api\Billings\NewBillingService;
use Supp\Api\Billings\NewBillingServiceFactory;
use Supp\Api\Billings\OtherRolesDeleteBilling;
use Supp\Api\Billings\OtherRolesNewBilling;
use Supp\Api\Billings\OtherRolesUpdateBilling;
use Supp\Api\Billings\UpdateBillingService;
use Supp\Api\Billings\UpdateBillingServiceFactory;
use Supp\Api\Clients\AdminClients;
use Supp\Api\Clients\AdminNewCompany;
use Supp\Api\Clients\ClientService;
use Supp\Api\Clients\ClientServiceFactory;
use Supp\Api\Clients\NewCompanyService;
use Supp\Api\Clients\NewCompanyServiceFactory;
use Supp\Api\Clients\OtherRolesClients;
use Supp\Api\Clients\OtherRolesNewCompany;
use Supp\Api\Comments\NewUploadCommentService;
use Supp\Api\Faqs\FaqService;
use Supp\Api\Models\Billing;
use Supp\Api\Models\BillingBase;
use Supp\Api\Models\Uploads;
use Supp\Api\Projects\AdminNewProject;
use Supp\Api\Projects\DeleteProjectsService;
use Supp\Api\Projects\DeleteProjectsServiceFactory;
use Supp\Api\Projects\GetProjectsService;
use Supp\Api\Projects\GetProjectsServiceFactory;
use Supp\Api\Projects\NewProjectService;
use Supp\Api\Projects\NewProjectServiceFactory;
use Supp\Api\Projects\OtherRolesDeleteProject;
use Supp\Api\Projects\OtherRolesNewProject;
use Supp\Api\Projects\OtherRolesProjets;
use Supp\Api\Projects\OtherRolesProjetsBilling;
use Supp\Api\Projects\ProjectsBillingService;
use Supp\Api\Projects\ProjectsBillingServiceFactory;
use Supp\Api\Projects\SuperAdminDeleteProject;
use Supp\Api\Projects\SuperAdminProjects;
use Supp\Api\Projects\SuperAdminProjectsBilling;
use Supp\Api\Signup\SignupService;
use Supp\Api\Statuses\OtherRolesStatuses;
use Supp\Api\Statuses\StatusService;
use Supp\Api\Statuses\StatusServiceFactory;
use Supp\Api\Statuses\SuperAdminStatuses;
use Supp\Api\TicketCategories\GetTicketCategories;
use Supp\Api\Tickets\AdminGetAllTickets;
use Supp\Api\Tickets\AdminPostTicketComment;
use Supp\Api\Tickets\AdminSingleTicket;
use Supp\Api\Tickets\AdminTicketComments;
use Supp\Api\Tickets\AssignTicketService;
use Supp\Api\Tickets\AssignTicketServiceFactory;
use Supp\Api\Tickets\BaseAllTickets;
use Supp\Api\Tickets\BaseGetSingleTicket;
use Supp\Api\Tickets\BaseNewTicket;
use Supp\Api\Tickets\BasePostTicketComment;
use Supp\Api\Tickets\BaseTicketComments;
use Supp\Api\Tickets\BugReport;
use Supp\Api\Tickets\ChangeOrder;
use Supp\Api\Tickets\CurrentUserSingleTicket;
use Supp\Api\Tickets\FeatureRequest;
use Supp\Api\Tickets\GetAllTicketsService;
use Supp\Api\Tickets\GetAllTicketsServiceFactory;
use Supp\Api\Tickets\GetSingleTicketService;
use Supp\Api\Tickets\GetSingleTicketServiceFactory;
use Supp\Api\Tickets\GetTicketsByCategoryService;
use Supp\Api\Tickets\GetTicketsByCategoryServiceFactory;
use Supp\Api\Tickets\GetTicketsByStatusService;
use Supp\Api\Tickets\GetTicketsByStatusServiceFactory;
use Supp\Api\Tickets\NewTicketCommentService;
use Supp\Api\Tickets\NewTicketCommentServiceFactory;
use Supp\Api\Tickets\NewTicketService;
use Supp\Api\Tickets\NewTicketServiceFactory;
use Supp\Api\Tickets\NewUploadTicketService;
use Supp\Api\Tickets\NullGetSingleTicket;
use Supp\Api\Tickets\OtherRolesAssignTicket;
use Supp\Api\Tickets\OtherRolesGetAllTickets;
use Supp\Api\Tickets\OtherRolesPostTicketComment;
use Supp\Api\Tickets\OtherRolesSingleTicket;
use Supp\Api\Tickets\OtherRolesTicketComments;
use Supp\Api\Tickets\OtherRolesTicketsByCategory;
use Supp\Api\Tickets\OtherRolesTicketsByStatus;
use Supp\Api\Tickets\OtherRolesTicketsReport;
use Supp\Api\Tickets\OtherRolesUpdateTicketStatus;
use Supp\Api\Tickets\SuperAdminAssignTicket;
use Supp\Api\Tickets\SuperAdminTicketsByCategory;
use Supp\Api\Tickets\SuperAdminTicketsByStatus;
use Supp\Api\Tickets\SuperAdminTicketsReport;
use Supp\Api\Tickets\SuperAdminUpdateTicketStatus;
use Supp\Api\Tickets\TicketCommentsService;
use Supp\Api\Tickets\TicketCommentsServiceFactory;
use Supp\Api\Tickets\TicketsReportService;
use Supp\Api\Tickets\TicketsReportServiceFactory;
use Supp\Api\Tickets\UpdateTicketStatusService;
use Supp\Api\Tickets\UpdateTicketStatusServiceFactory;
use Supp\Api\Time\TimeConverter;
use Supp\Api\Users\AdminBillings;
use Supp\Api\Users\GetUserMyService;
use Supp\Api\Users\GetUsersAdmins;
use Supp\Api\Users\GetUsersOtherRoles;
use Supp\Api\Users\GetUsersService;
use Supp\Api\Users\GetUsersServiceFactory;
use Supp\Api\Users\OtherRolesBillings;
use Supp\Api\Users\SuperAdminBillings;
use Supp\Api\Users\UserBillingsService;
use Supp\Api\Users\UserBillingsServiceFactory;
use Supp\SuppApiService;

function getDatabaseConnection(Container $container): PDO
{
    $config = $container->get('config');

    $dsn = sprintf('mysql:dbname=%s;host=%s', $config->get("database.database"), $config->get("database.host"));

    try {
        $dbh = new PDO($dsn, $config->get("database.username"), $config->get("database.password"), [PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        throw new Exception("Connection failed: " . $e->getMessage());
    }

    return $dbh;

}

/**
 * @param $config_values
 * @return \League\Container\Container
 * @throws \Exception
 */
function getContainer($config_values)
{

    $responseFactory = new ResponseFactory();
    $container = new Container();

    try {

        $configs = getConfigs($config_values);
        $container->add('config', $configs);
        $container->add('db', getDatabaseConnection($container));

        $container->add(ApplicationStrategy::class);
        $container->add(JsonStrategy::class)->addArgument($responseFactory);
        $container->add(Router::class);
//        $container->add(Reader::class);
//        $container->add(Writer::class);

        $container->add(RandomGenerator::class);
        $container->add(DateTime::class);
        $container->add(QueryRunner::class)->addArgument($container);
        $container->add(TimeConverter::class)->addArgument($container);

        /* API services */
        $container->add(SuppApiService::class)->addArgument($container);

        /* Auth services */
        $container->add(NonceService::class)->addArgument($container);
        $container->add(Login::class)->addArgument($container);
        $container->add(User::class)->addArgument($container);
        $container->add(UserToken::class)->addArgument($container);
        $container->add(UserTokenBase::class)->addArgument($container);
        $container->add(ApiService::class)->addArgument($container);
//        $container->add(User::class)->addArgument($container);
//        $container->add(\Hph\Api\Models\UserBase::class)->addArgument($container);
//        $container->add(UserTokenBase::class)->addArgument($container);
//        $container->add(UserToken::class)->addArgument($container);
//        $container->add(UserInfo::class)->addArgument($container);
        $container->add(UserNav::class)->addArgument($container);

//        $container->add(UserSubscriptionsService::class);
        /* <signup> */
        $container->add(SignupService::class)->addArgument($container);
        /* <signup> */

        /* <ticket categories> */
        $container->add(GetTicketCategories::class)->addArgument($container);
        /* <ticket categories> */

        /* <password_management> */
        //$container->add(UserActivationService::class)->addArgument($container);
        //$container->add(AccountRecoveryService::class)->addArgument($container);
        $container->add(PasswordResetService::class)->addArgument($container);
        /* </password_management> */

        $container->add(NewTicketService::class)->addArgument($container);
        $container->add(NewTicketServiceFactory::class)->addArgument($container);
        $container->add(BaseNewTicket::class)->addArgument($container);
        $container->add(BugReport::class)->addArgument($container);
        $container->add(ChangeOrder::class)->addArgument($container);
        $container->add(FeatureRequest::class)->addArgument($container);

        $container->add(AssignTicketService::class)->addArgument($container);
        $container->add(AssignTicketServiceFactory::class)->addArgument($container);
        $container->add(SuperAdminAssignTicket::class)->addArgument($container);
        $container->add(OtherRolesAssignTicket::class)->addArgument($container);

        $container->add(TicketCommentsService::class)->addArgument($container);
        $container->add(TicketCommentsServiceFactory::class)->addArgument($container);
        $container->add(AdminTicketComments::class)->addArgument($container);
        $container->add(BaseTicketComments::class)->addArgument($container);
        $container->add(OtherRolesTicketComments::class)->addArgument($container);

        $container->add(AdminPostTicketComment::class)->addArgument($container);
        $container->add(BasePostTicketComment::class)->addArgument($container);
        $container->add(NewTicketCommentService::class)->addArgument($container);
        $container->add(NewTicketCommentServiceFactory::class)->addArgument($container);
        $container->add(OtherRolesPostTicketComment::class)->addArgument($container);

        $container->add(AdminGetAllTickets::class)->addArgument($container);
        $container->add(BaseAllTickets::class)->addArgument($container);
        $container->add(GetAllTicketsService::class)->addArgument($container);
        $container->add(GetAllTicketsServiceFactory::class)->addArgument($container);
        $container->add(OtherRolesGetAllTickets::class)->addArgument($container);

        $container->add(GetUserMyService::class)->addArgument($container);

        $container->add(AdminNewCompany::class)->addArgument($container);
        $container->add(NewCompanyService::class)->addArgument($container);
        $container->add(NewCompanyServiceFactory::class)->addArgument($container);
        $container->add(OtherRolesNewCompany::class)->addArgument($container);

        $container->add(AdminClients::class)->addArgument($container);
        $container->add(ClientService::class)->addArgument($container);
        $container->add(ClientServiceFactory::class)->addArgument($container);
        $container->add(OtherRolesClients::class)->addArgument($container);

        $container->add(NewProjectService::class)->addArgument($container);
        $container->add(NewProjectServiceFactory::class)->addArgument($container);
        $container->add(AdminNewProject::class)->addArgument($container);
        $container->add(OtherRolesNewProject::class)->addArgument($container);

        $container->add(DeleteProjectsService::class)->addArgument($container);
        $container->add(DeleteProjectsServiceFactory::class)->addArgument($container);
        $container->add(OtherRolesDeleteProject::class)->addArgument($container);
        $container->add(SuperAdminDeleteProject::class)->addArgument($container);

        $container->add(GetProjectsService::class)->addArgument($container);
        $container->add(GetProjectsServiceFactory::class)->addArgument($container);
        $container->add(SuperAdminProjects::class)->addArgument($container);
        $container->add(OtherRolesProjets::class)->addArgument($container);
        $container->add(AdminSingleTicket::class)->addArgument($container);
        $container->add(BaseGetSingleTicket::class)->addArgument($container);
        $container->add(CurrentUserSingleTicket::class)->addArgument($container);
        $container->add(GetSingleTicketService::class)->addArgument($container);
        $container->add(GetSingleTicketServiceFactory::class)->addArgument($container);
        $container->add(NullGetSingleTicket::class)->addArgument($container);
        $container->add(OtherRolesSingleTicket::class)->addArgument($container);

        $container->add(UpdateTicketStatusService::class)->addArgument($container);
        $container->add(UpdateTicketStatusServiceFactory::class)->addArgument($container);
        $container->add(SuperAdminUpdateTicketStatus::class)->addArgument($container);
        $container->add(OtherRolesUpdateTicketStatus::class)->addArgument($container);

        $container->add(TicketsReportService::class)->addArgument($container);
        $container->add(TicketsReportServiceFactory::class)->addArgument($container);
        $container->add(SuperAdminTicketsReport::class)->addArgument($container);
        $container->add(OtherRolesTicketsReport::class)->addArgument($container);

        $container->add(GetTicketsByStatusService::class)->addArgument($container);
        $container->add(GetTicketsByStatusServiceFactory::class)->addArgument($container);
        $container->add(SuperAdminTicketsByStatus::class)->addArgument($container);
        $container->add(OtherRolesTicketsByStatus::class)->addArgument($container);

        $container->add(GetTicketsByCategoryService::class)->addArgument($container);
        $container->add(GetTicketsByCategoryServiceFactory::class)->addArgument($container);
        $container->add(SuperAdminTicketsByCategory::class)->addArgument($container);
        $container->add(OtherRolesTicketsByCategory::class)->addArgument($container);

        $container->add(NewBillingService::class)->addArgument($container);
        $container->add(NewBillingServiceFactory::class)->addArgument($container);
        $container->add(AdminNewBilling::class)->addArgument($container);
        $container->add(OtherRolesNewBilling::class)->addArgument($container);

        $container->add(UpdateBillingService::class)->addArgument($container);
        $container->add(UpdateBillingServiceFactory::class)->addArgument($container);
        $container->add(AdminUpdateBilling::class)->addArgument($container);
        $container->add(OtherRolesUpdateBilling::class)->addArgument($container);

        $container->add(DeleteBillingService::class)->addArgument($container);
        $container->add(DeleteBilingServiceFactory::class)->addArgument($container);
        $container->add(AdminDeleteBilling::class)->addArgument($container);
        $container->add(OtherRolesDeleteBilling::class)->addArgument($container);

        $container->add(UserBillingsService::class)->addArgument($container);
        $container->add(UserBillingsServiceFactory::class)->addArgument($container);
        $container->add(AdminBillings::class)->addArgument($container);
        $container->add(SuperAdminBillings::class)->addArgument($container);
        $container->add(OtherRolesBillings::class)->addArgument($container);

        $container->add(ProjectsBillingService::class)->addArgument($container);
        $container->add(ProjectsBillingServiceFactory::class)->addArgument($container);
        $container->add(SuperAdminProjectsBilling::class)->addArgument($container);
        $container->add(OtherRolesProjetsBilling::class)->addArgument($container);

        $container->add(ExportBillingService::class)->addArgument($container);
        $container->add(ExportBillingServiceFactory::class)->addArgument($container);
        $container->add(Today::class)->addArgument($container);
        $container->add(Yesterday::class)->addArgument($container);
        $container->add(ThisWeek::class)->addArgument($container);
        $container->add(LastWeek::class)->addArgument($container);
        $container->add(Last30days::class)->addArgument($container);
        $container->add(ThisMonth::class)->addArgument($container);
        $container->add(LastMonth::class)->addArgument($container);
        $container->add(Last90days::class)->addArgument($container);
        $container->add(Last12months::class)->addArgument($container);
        $container->add(ThisYear::class)->addArgument($container);
        $container->add(LastYear::class)->addArgument($container);
        $container->add(GetAllBillingsExportService::class)->addArgument($container);

        $container->add(FaqService::class)->addArgument($container);

        $container->add(NewUploadTicketService::class)->addArgument($container);
        $container->add(NewUploadCommentService::class)->addArgument($container);
        $container->add(Uploads::class)->addArgument($container);
        $container->add(Billing::class)->addArgument($container);
        $container->add(BillingBase::class)->addArgument($container);
        $container->add(StatusService::class)->addArgument($container);
        $container->add(StatusServiceFactory::class)->addArgument($container);
        $container->add(SuperAdminStatuses::class)->addArgument($container);
        $container->add(OtherRolesStatuses::class)->addArgument($container);

        $container->add(GetUsersService::class)->addArgument($container);
        $container->add(GetUsersServiceFactory::class)->addArgument($container);
        $container->add(GetUsersAdmins::class)->addArgument($container);
        $container->add(GetUsersOtherRoles::class)->addArgument($container);


    } catch (Exception $e) {
        echo "<pre>";
        echo "Container problem. Error ({$e->getCode()}) . {$e->getMessage()} in file {$e->getFile()}:{$e->getLine()}";
        echo "Trace:";
        var_dump($e->getTraceAsString());
        echo "</pre>";
        die(__FILE__ . ":" . __LINE__);
    }


    return $container;
}
