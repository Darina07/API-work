<?php declare(strict_types=1);

namespace Tests;

use DateTime;
use DateTimeZone;
use Exception;
use Hphio\Auth\Models\User;
use hphio\util\RandomGenerator;
use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use League\Csv\Reader;
use League\Csv\Writer;
use PDO;
use PDOException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
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
use Supp\Api\Auth\Users\PasswordResetService;
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
use Tests\Api\FileUploadTrait;
use Tests\Api\Tickets\NewTicketServiceTest;

class SuppTest extends TestCase
{
    static protected ?PDO $pdo = null;
    protected ?PDO $conn = null;
    protected ?Container $container = null;

    protected ?string $defaultsFilePath = null;
    protected ?MockObject $request = null;
    protected $serverParams = [];

    protected function getContainer()
    {
        return $this->container;
    }

    protected function buildContainer($filter = []): SuppTest
    {
        unset($this->container);
        $this->container = new Container();

        $this->loadContainerizedClasses($filter);

        return $this;
    }

    /**
     * alias of mockDateTime().
     * @return $this
     */
    protected function withDejaVu($timestamp = 728654400, $timezone = "GMT"): SuppTest
    {
        return $this->mockDateTime($timestamp, $timezone);
    }

    /**
     * Mock DateTime so that it always returns Groundhog Day of 1993.
     * See: https://www.imdb.com/title/tt0107048/
     * @return $this
     */
    protected function mockDateTime($timestamp = 728654400, $timezone = "GMT"): SuppTest
    {
        $mockDateTime = $this->getMockBuilder(DateTime::class)
            ->setConstructorArgs(["@" . $timestamp, new DateTimeZone($timezone)])
            ->onlyMethods([]) //Allow all original methods to work.
            ->getMock();

        $this->container->add(DateTime::class, $mockDateTime);

        return $this;
    }

    /**
     * Mock PDO for the database.
     * @return $this
     */
    protected function mockDB(): SuppTest
    {
        $mockPDO = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->add('db', $mockPDO);

        return $this;
    }

    private function UserServices(array $containerizedClasses): array
    {

        $containerizedClasses[] = GetTicketCategories::class;
        $containerizedClasses[] = SignupService::class;
        $containerizedClasses[] = QueryRunner::class;
        $containerizedClasses[] = NewTicketService::class;
        $containerizedClasses[] = NewTicketServiceFactory::class;
        $containerizedClasses[] = BaseNewTicket::class;
        $containerizedClasses[] = BugReport::class;
        $containerizedClasses[] = ChangeOrder::class;
        $containerizedClasses[] = FeatureRequest::class;
        $containerizedClasses[] = PasswordResetService::class;
        $containerizedClasses[] = AssignTicketService::class;
        $containerizedClasses[] = AssignTicketServiceFactory::class;
        $containerizedClasses[] = SuperAdminAssignTicket::class;
        $containerizedClasses[] = OtherRolesAssignTicket::class;

        $containerizedClasses[] = TicketCommentsService::class;
        $containerizedClasses[] = TicketCommentsServiceFactory::class;
        $containerizedClasses[] = AdminTicketComments::class;
        $containerizedClasses[] = BaseTicketComments::class;
        $containerizedClasses[] = OtherRolesTicketComments::class;

        $containerizedClasses[] = AdminPostTicketComment::class;
        $containerizedClasses[] = BasePostTicketComment::class;
        $containerizedClasses[] = NewTicketCommentService::class;
        $containerizedClasses[] = NewTicketCommentServiceFactory::class;
        $containerizedClasses[] = OtherRolesPostTicketComment::class;

        $containerizedClasses[] = AdminGetAllTickets::class;
        $containerizedClasses[] = BaseAllTickets::class;
        $containerizedClasses[] = GetAllTicketsService::class;
        $containerizedClasses[] = GetAllTicketsServiceFactory::class;
        $containerizedClasses[] = OtherRolesGetAllTickets::class;

        $containerizedClasses[] = GetUserMyService::class;
        $containerizedClasses[] = NewProjectService::class;
        $containerizedClasses[] = NewProjectServiceFactory::class;
        $containerizedClasses[] = AdminNewProject::class;
        $containerizedClasses[] = OtherRolesNewProject::class;
        $containerizedClasses[] = GetProjectsService::class;
        $containerizedClasses[] = GetProjectsServiceFactory::class;
        $containerizedClasses[] = SuperAdminProjects::class;
        $containerizedClasses[] = OtherRolesProjets::class;

        $containerizedClasses[] = DeleteProjectsService::class;
        $containerizedClasses[] = DeleteProjectsServiceFactory::class;
        $containerizedClasses[] = SuperAdminDeleteProject::class;
        $containerizedClasses[] = OtherRolesDeleteProject::class;

        $containerizedClasses[] = AdminNewCompany::class;
        $containerizedClasses[] = NewCompanyService::class;
        $containerizedClasses[] = NewCompanyServiceFactory::class;
        $containerizedClasses[] = OtherRolesNewCompany::class;

        $containerizedClasses[] = AdminClients::class;
        $containerizedClasses[] = ClientService::class;
        $containerizedClasses[] = ClientServiceFactory::class;
        $containerizedClasses[] = OtherRolesClients::class;

        $containerizedClasses[] = AdminSingleTicket::class;
        $containerizedClasses[] = BaseGetSingleTicket::class;
        $containerizedClasses[] = CurrentUserSingleTicket::class;
        $containerizedClasses[] = GetSingleTicketService::class;
        $containerizedClasses[] = GetSingleTicketServiceFactory::class;
        $containerizedClasses[] = NullGetSingleTicket::class;
        $containerizedClasses[] = OtherRolesSingleTicket::class;

        $containerizedClasses[] = UpdateTicketStatusService::class;
        $containerizedClasses[] = UpdateTicketStatusServiceFactory::class;
        $containerizedClasses[] = SuperAdminUpdateTicketStatus::class;
        $containerizedClasses[] = OtherRolesUpdateTicketStatus::class;

        $containerizedClasses[] = TicketsReportService::class;
        $containerizedClasses[] = TicketsReportServiceFactory::class;
        $containerizedClasses[] = SuperAdminTicketsReport::class;
        $containerizedClasses[] = OtherRolesTicketsReport::class;

        $containerizedClasses[] = GetTicketsByStatusService::class;
        $containerizedClasses[] = GetTicketsByStatusServiceFactory::class;
        $containerizedClasses[] = SuperAdminTicketsByStatus::class;
        $containerizedClasses[] = OtherRolesTicketsByStatus::class;

        $containerizedClasses[] = GetTicketsByCategoryService::class;
        $containerizedClasses[] = GetTicketsByCategoryServiceFactory::class;
        $containerizedClasses[] = SuperAdminTicketsByCategory::class;
        $containerizedClasses[] = OtherRolesTicketsByCategory::class;

        $containerizedClasses[] = NewUploadTicketService::class;
        $containerizedClasses[] = NewUploadCommentService::class;
        $containerizedClasses[] = Uploads::class;

        $containerizedClasses[] = BillingBase::class;
        $containerizedClasses[] = Billing::class;

        $containerizedClasses[] = NewBillingService::class;
        $containerizedClasses[] = NewBillingServiceFactory::class;
        $containerizedClasses[] = AdminNewBilling::class;
        $containerizedClasses[] = OtherRolesNewBilling::class;
        $containerizedClasses[] = UpdateBillingService::class;
        $containerizedClasses[] = UpdateBillingServiceFactory::class;
        $containerizedClasses[] = AdminUpdateBilling::class;
        $containerizedClasses[] = OtherRolesUpdateBilling::class;

        $containerizedClasses[] = DeleteBillingService::class;
        $containerizedClasses[] = DeleteBilingServiceFactory::class;
        $containerizedClasses[] = AdminDeleteBilling::class;
        $containerizedClasses[] = OtherRolesDeleteBilling::class;

        $containerizedClasses[] = UserBillingsService::class;
        $containerizedClasses[] = UserBillingsServiceFactory::class;
        $containerizedClasses[] = AdminBillings::class;
        $containerizedClasses[] = SuperAdminBillings::class;
        $containerizedClasses[] = OtherRolesBillings::class;
        $containerizedClasses[] = ProjectsBillingService::class;
        $containerizedClasses[] = ProjectsBillingServiceFactory::class;
        $containerizedClasses[] = SuperAdminProjectsBilling::class;
        $containerizedClasses[] = OtherRolesProjetsBilling::class;

        $containerizedClasses[] = ExportBillingService::class;
        $containerizedClasses[] = ExportBillingServiceFactory::class;
        $containerizedClasses[] = Today::class;
        $containerizedClasses[] = Yesterday::class;
        $containerizedClasses[] = ThisWeek::class;
        $containerizedClasses[] = LastWeek::class;
        $containerizedClasses[] = Last30days::class;
        $containerizedClasses[] = ThisMonth::class;
        $containerizedClasses[] = LastMonth::class;
        $containerizedClasses[] = Last90days::class;
        $containerizedClasses[] = Last12months::class;
        $containerizedClasses[] = ThisYear::class;
        $containerizedClasses[] = LastYear::class;
        $containerizedClasses[] = GetAllBillingsExportService::class;

        $containerizedClasses[] = FaqService::class;
        $containerizedClasses[] = StatusService::class;
        $containerizedClasses[] = StatusServiceFactory::class;
        $containerizedClasses[] = SuperAdminStatuses::class;
        $containerizedClasses[] = OtherRolesStatuses::class;

        $containerizedClasses[] = GetUsersService::class;
        $containerizedClasses[] = GetUsersServiceFactory::class;
        $containerizedClasses[] = GetUsersAdmins::class;
        $containerizedClasses[] = GetUsersOtherRoles::class;

//        $containerizedClasses[] = Login::class;
//        $containerizedClasses[] = User::class;
//        $containerizedClasses[] = UserBase::class;
//        $containerizedClasses[] = UserTokenBase::class;
//        $containerizedClasses[] = UserToken::class;
//        $containerizedClasses[] = Authenticator::class;
//        $containerizedClasses[] = ImpersonatePermissions::class;
//        $containerizedClasses[] = UserInfo::class;
//        $containerizedClasses[] = UserNav::class;
//        $containerizedClasses[] = DeleteUserService::class;

        return $containerizedClasses;
    }



  /**
     * Load all the classes that require the container in their constructor.
     * @param $filter
     * @param $container
     * @return void
     */
    private function loadContainerizedClasses($filter): void
    {
        $containerizedClasses = [];
        $containerizedClasses = $this->UserServices($containerizedClasses);



        $finalClasses = array_diff($containerizedClasses, $filter);
        foreach ($finalClasses as $class) {
            $this->container->add($class)->addArgument($this->container);
        }
    }



    /**
     * Add the system config into the container.
     * @return $this
     */
    protected function withConfig()
    {
        $this->container->add('config', getConfigs(getConfigValues()));
        return $this;
    }

    private function DatabaseTools(array $containerizedClasses)
    {
        $containerizedClasses[] = QueryRunner::class;
        return $containerizedClasses;
    }

    /**
     * Mock the HPHIO\RandomGenerator so that it gives determinant, consecutive, predictable values.
     * @return void
     */
    protected function withoutRandomness(): SuppTest
    {
        $notSoRandom = $this->createMock(RandomGenerator::class);
        $notSoRandom->method('uuidv4')->will(
            $this->onConsecutiveCalls(
                '9f052830-c39a-4bac-81a2-64bf78c38030',
                "0742a8c0-47ec-4ade-a1fa-916ee7d48f15",
                "4c47a109-e754-4316-b7d0-c58e11daad71",
                "dd723f07-93b7-4f12-b226-0ab57f8e93b8",
                "643b7d2c-7c37-46fa-832b-37bfa3999ccc",
                "e98247f6-df59-4ece-a299-f97f6e25f3d9",
                "abc9f316-85a5-48b7-b195-2ec25d0c4b1c",
                "efa0e957-69e4-4d6f-921c-fb4239812a8b",
                "6442729c-e962-4482-88ba-7ca1a6aa80dd",
                "618506c2-9b2d-42dd-b3a8-03e1b9bb5ef7",
                "416f2434-a3b9-4ce6-b1e6-75168fe682cb",
                "55e9abf2-1636-44d0-94a3-af74611107bb",
                "e94360f8-12d5-4bd3-aad9-62c69211d8e1",
                "b51777a4-e70a-4aa4-86b3-63baf6df47f9",
                "0b1e6833-c0fb-452f-a0ae-318b4c5d096c"
            )
        );

        $this->container->add(RandomGenerator::class, $notSoRandom);
        return $this;
    }

    /**
     * Provide a mock PHPMailer instance to test email sends.
     */

    protected function withNotifications() : SuppTest {
        $mockMailer = $this->getMockBuilder(PHPMailer::class)
            ->onlyMethods(['send']) //Allow all original methods to work.
            ->getMock();

        $mockMailer->method('send')->willReturn(true);

        $this->container->add(DummyEmailEnabledService::class)->addArgument($this->container);
        $this->container->add(PHPMailer::class, $mockMailer);
        return $this;
    }

    /**
     * Add the database connection configured in phpunit.xml to the container as 'db'.
     * @return void
     */
    protected function withDatabase(): SuppTest
    {
        if ($this->conn === null)
            $this->connectToDatabase();
        $this->container->add('db', $this->conn);
        return $this;
    }

    private function connectToDatabase()
    {
        if (self::$pdo == null) {
            try {
                self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], [PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
                self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("Connection failed: " . $e->getMessage());
            }
        }

        $this->conn = self::$pdo;
    }

    /**
     * Load the dataset for this test.
     */
    protected function loadDataSet()
    {

        $sqlFile = $this->getFixturePath();

        $this->writeDefaultsFile();
        $this->importSql($sqlFile);
        $this->destroyDefaultsFile();
    }

    private function importSql($sqlFile)
    {
        if (!file_exists($sqlFile))
            throw new Exception("$sqlFile does not exist.");

        $cmd = 'mysql --defaults-file="{FILE}" support_local < {SQLFILE}';
        $cmd = str_replace("{FILE}", $this->defaultsFilePath, $cmd);
        $cmd = str_replace("{SQLFILE}", $sqlFile, $cmd);
        $buffer = shell_exec($cmd);
    }

    protected function writeDefaultsFile()
    {
        $template = <<<EOF
[client]
user={USER}
password={PASS}

[mysqladmin]
user={USER}
password={PASS}

EOF;

        if ($this->defaultsFilePath === null)
            $this->defaultsFilePath = tempnam(sys_get_temp_dir(), "tmp_");

        $fh = fopen($this->defaultsFilePath, 'w');
        $output = str_replace("{USER}", $GLOBALS['DB_USER'], $template);
        $output = str_replace("{PASS}", $GLOBALS['DB_PASSWD'], $output);

        fwrite($fh, $output);
        fclose($fh);
    }

    protected function destroyDefaultsFile()
    {
        if (file_exists($this->defaultsFilePath))
            unlink($this->defaultsFilePath);
    }

    protected function getFixturePath()
    {
        //Build the path from the app root (stored in bootstrap.php as a $GLOBAL)
        $fixturePath = explode('.', $GLOBALS['appRoot']);

        //add 'tests/' to the base path.
        array_push($fixturePath, 'tests');

        // Build the rest of the path from the namespace and file name.
        //Get the class, and pull of the "Tests" because we already added the 'tests/' directory.
        $className = get_class($this);
        $reflection_class = new ReflectionClass($className);
        $namespace = $reflection_class->getNamespaceName();
        $nsBuffer = explode('\\', $namespace);
        array_shift($nsBuffer);
        array_push($nsBuffer, "fixtures");

        //add these to the figure path:
        $fixturePath = array_merge($fixturePath, $nsBuffer);

        //Finally, add the file name on.
        $filenameBuffer = explode('\\', $className);
        $sqlFile = array_pop($filenameBuffer) . '.sql';
        array_push($fixturePath, $sqlFile);

        $sqlFile = implode('/', $fixturePath);
        return $sqlFile;
    }

    public function withCurrentTime()
    {
        $now = new DateTime();
        $this->container->add(DateTime::class, $now);
        return $this;
    }

    /**
     * Begin to build out a mock server request. Sets $this->request to the mock object.
     * @return ERCTest
     */
    public function buildMockServerRequest(): SuppTest
    {
        $this->request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
        return $this;
    }

    /**
     * Add a request body (typically JSON) to the payload.
     * $body = '{"message":"hello world."}';
     * $request = $this->buildMockServerRequest()
     *      ->withRequestBody($body)
     *      ->getRequest();
     * @param string $body
     * @return ERCTest
     */
    public function withRequestBody(string $body): SuppTest
    {
        $jsonPayload = $body;
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $this->request->method('getBody')->willReturn(new Stream($stream));
        return $this;
    }

    /**
     * Add server parameters to the request.
     * $serverParams = [
     *      'REQUEST_METHOD' => $method
     *      , 'REQUEST_URI'  => $uri
     *  ];
     * $request = $this->buildMockServerRequest()
     *      ->withServerParams($serverParams)
     *      ->getRequest();
     * @param array $serverParams
     * @return ERCTest
     */
    public function addServerParam(string $parameter, $value): SuppTest
    {
        $this->serverParams[$parameter] = $value;
        return $this;
    }

    /**
     * @param $uri
     * @return void
     * Set the server request URI for the request.
     * $request = $this->buildMockServerRequest()
     *      ->useRequestURI('/clients/100')
     *      ->getRequest();
     */
    public function useRequestURI($uri): SuppTest
    {
        $uri = "/api/v1" . $uri;
        $mockURI = $this->getMockBuilder(UriInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockURI->method('getPath')->willReturn($uri);
        $this->request->method('getUri')->willReturn($mockURI);
        $this->addServerParam('REQUEST_URI', $uri);
        return $this;
    }

    /**
     * Set the HTTP VERB (Method) of the request.
     * $request = $this->buildMockServerRequest()
     *      ->useMethod('POST')
     *      ->getRequest();
     * @param string $method
     * @return ERCTest
     */
    public function useMethod(string $method = 'GET'): SuppTest
    {
        $this->addServerParam('REQUEST_METHOD', $method);
        $this->request->method('getMethod')->willReturn($method);

        return $this;
    }

    /**
     * Adds headers to the requst.
     * $defaultHeaders = [
     * 'Authentication' => 'Bearer eyJ0eXAiOiJ',
     * 'Content-Type' => 'application/json'
     * ];
     * $request = $this->buildMockServerRequest()
     *      ->withHeaders($defaultHeaders)
     *      ->getRequest();
     * @param array $headers
     * @return ERCTest
     */
    public function withHeaders(array $headers): SuppTest
    {
        $this->request->method('getHeaders')->willReturn($headers);
        $hasHeaderMap = $this->renderHasHeaderMap($headers);
        $getHeaderMap = $this->renderGetHeaderMap($headers);

        $this->request->method('hasHeader')
            ->will($this->returnValueMap($hasHeaderMap));

        $this->request->method('getHeader')
            ->will($this->returnValueMap($getHeaderMap));

        return $this;
    }

    /**
     * Return applies the server params, if any, and then returns the mock server request.
     */
    public function getRequest()
    {
        $this->request->method('getServerParams')->willReturn($this->serverParams);
        return $this->request;
    }

    /**
     * Builds the header map to tell the request if a header exists or not.
     * @param array $headers
     * @return void
     */
    private function renderHasHeaderMap(array $headers): array
    {
        $hasHeaderMap = [];
        foreach ($headers as $header => $value) {
            $hasHeaderMap[] = [$header, true];
        }

        return $hasHeaderMap;
    }

    /**
     * Build the header map to tell the request the value of each header.
     * @param array $headers
     * @return array
     */
    private function renderGetHeaderMap(array $headers): array
    {
        $getHeaderMap = [];
        foreach ($headers as $header => $value) {
            $getHeaderMap[] = [$header, [$value]];
        }

        return $getHeaderMap;
    }

    private function EntityLeadServices(array $containerizedClasses): array
    {
        //Post
        $containerizedClasses[] = AdminNewLeadService::class;
        $containerizedClasses[] = ManagingPartnerNewLeadService::class;
        $containerizedClasses[] = ContractorNewLeadService::class;
        $containerizedClasses[] = NewEntityLeadService::class;
        $containerizedClasses[] = BaseNewLeadsService::class;
        $containerizedClasses[] = NewLeadServicePermissionDenied::class;
        $containerizedClasses[] = GetLead::class;

        //Get
        $containerizedClasses[] = AdminGetLeadsService::class;
        $containerizedClasses[] = ManagingPartnerGetLeadsService::class;
        $containerizedClasses[] = ContractorGetLeadsService::class;
        $containerizedClasses[] = GetEntityLeadSearchService::class;

        $containerizedClasses[] = AdminSingleLeadService::class;
        $containerizedClasses[] = ManagingPartnerSingleLeadService::class;
        $containerizedClasses[] = ContractorSingleLeadService::class;
        $containerizedClasses[] = GetSingleLeadService::class;

        return $containerizedClasses;
    }

    public function withCurrentUser(User $user): SuppTest
    {
        $this->container->add('current_user', $user);
        return $this;
    }

    private function LeadStatus(array $containerizedClasses)
    {
        $containerizedClasses[] = StatusDecorator::class;
        $containerizedClasses[] = LeadStatusService::class;
        return $containerizedClasses;
    }

    private function LeadNotes(array $containerizedClasses)
    {
        $containerizedClasses[] = GetLeadNoteService::class;
        $containerizedClasses[] = NewLeadNoteService::class;
        $containerizedClasses[] = UpdateLeadNoteService::class;
        $containerizedClasses[] = DeleteLeadNoteService::class;
        return $containerizedClasses;
    }

    private function UpdateLead(array $containerizedClasses)
    {
        $containerizedClasses[] = UpdateLeadServicePermissionsDenied::class;
        $containerizedClasses[] = ContractorUpdateLeadService::class;
        $containerizedClasses[] = ManagingPartnerUpdateLeadService::class;
        $containerizedClasses[] = Promoter::class;
        $containerizedClasses[] = PromoterService::class;
        $containerizedClasses[] = DeleteLeadService::class;
        $containerizedClasses[] = DeleteLeadServiceFactory::class;
        $containerizedClasses[] = NullDeleteLead::class;
        $containerizedClasses[] = PeopleAlowedToDelete::class;

        return $containerizedClasses;
    }

    private function RequiredDocuments(array $containerizedClasses): array
    {
        $containerizedClasses[] = RequiredDocumentService::class;
        $containerizedClasses[] = RequiredClientDocumentService::class;
        $containerizedClasses[] = ClientRequiredDocuments::class;
        return $containerizedClasses;
    }

    private function ClientReportDocuments(array $containerizedClasses)
    {
        $containerizedClasses[] = ClientDocumentProgressReportService::class;
        $containerizedClasses[] = UserReportFactory::class;
        $containerizedClasses[] = BaseUserReportService::class;
        $containerizedClasses[] = GetUserReportsService::class;
        $containerizedClasses[] = AdminUserReportService::class;
        $containerizedClasses[] = ArchitectUserReportService::class;
        $containerizedClasses[] = TaxAttorneyUserReportService::class;
        $containerizedClasses[] = ManagingAttorneyUserReportService::class;
        $containerizedClasses[] = OfficeManagerUserReportService::class;
        $containerizedClasses[] = ClientUserReportService::class;
        $containerizedClasses[] = AttorneyUserReportService::class;
        $containerizedClasses[] = ManagingPartnerUserReportService::class;
        $containerizedClasses[] = ContractorUserReportService::class;

        return $containerizedClasses;
    }

    private function LeadReports(array $containerizedClasses)
    {
        $containerizedClasses[] = LeadReportService::class;
        $containerizedClasses[] = LeadReportServiceFactory::class;
        $containerizedClasses[] = AdminLeadReport::class;
        $containerizedClasses[] = NullLeadReport::class;
        $containerizedClasses[] = BaseLeadReport::class;
        return $containerizedClasses;
    }

    private function ResourcesUploads(array $containerizedClasses)
    {
        $containerizedClasses[] = ResourcesService::class;
        $containerizedClasses[] = ResourcesServiceFactory::class;
        $containerizedClasses[] = BaseResourcesService::class;
        $containerizedClasses[] = AdminResourcesService::class;
        $containerizedClasses[] = ArchitectResourcesService::class;
        $containerizedClasses[] = TaxAttorneyResourcesService::class;
        $containerizedClasses[] = ManagingAttorneyResourcesService::class;
        $containerizedClasses[] = ClientResourcesService::class;
        $containerizedClasses[] = OfficeManagerResourcesService::class;
        $containerizedClasses[] = AttorneyResourcesService::class;
        $containerizedClasses[] = ContractorResourcesService::class;
        $containerizedClasses[] = ManagingPartnerResourcesService::class;
        $containerizedClasses[] = GetResourcePageData::class;

        $containerizedClasses[] = PostUploadsServiceFactoryTest::class;
        $containerizedClasses[] = PostUploadsService::class;
        $containerizedClasses[] = AdminPostUploadsService::class;
        $containerizedClasses[] = BasePostUploadsService::class;
        $containerizedClasses[] = NullPostUploadsService::class;
        $containerizedClasses[] = UnsupportedPostUploadsService::class;
        $containerizedClasses[] = FileUploadTrait::class;
        return $containerizedClasses;
    }

    public function createdJustNow(string $datetime)
    {
        $timestamp = strtotime($datetime);
        $created = new DateTime('@' . $timestamp);
        $now = new DateTime();
        $diff = $now->diff($created);
        $success = true;
        $success = $success && ($diff->y == 0);
        $success = $success && ($diff->m == 0);
        $success = $success && ($diff->d == 0);
        $success = $success && ($diff->h == 0);
        $success = $success && ($diff->i == 0);
        $success = $success && ($diff->s < 59);
        return $success;
    }

    private function SanitizationQueueServices(array $containerizedClasses) {
        $containerizedClasses[] = SingleSanitizationJob::class;
        $containerizedClasses[] = NullSingleSanitizationJob::class;
        $containerizedClasses[] = NewSanitizationJobCommentsService::class;
        $containerizedClasses[] = NewSanitizationJobComment::class;
        $containerizedClasses[] = NullNewSanitizationJobComments::class;

        $containerizedClasses[] = PatchSingleSanitizationJobService::class;
        $containerizedClasses[] = NullPatchSingleSanitizationJob::class;
        $containerizedClasses[] = GetSingleSanitizationJobService::class;
        $containerizedClasses[] = BaseGetSingleSanitizationJobService::class;
        $containerizedClasses[] = PatchSanitizationJob::class;
        $containerizedClasses[] = PatchSingleSanitizationJobFactory::class;
        $containerizedClasses[] = GetSingleSanitizationJobComments::class;
        $containerizedClasses[] = GetSanitizeJobCommentsFactory::class;
        $containerizedClasses[] = GetSingleJobComments::class;
        return $containerizedClasses;
    }
    private function ScoreboardServices(array $containerizedClasses)
    {
        $containerizedClasses[] = AdminLeadsScoreboard::class;
        $containerizedClasses[] = ManagingAttorneyLeadsScoreboard::class;
        $containerizedClasses[] = ManagingPartnerLeadsScoreboard::class;
        $containerizedClasses[] = ContractorLeadsScoreboard::class;
        $containerizedClasses[] = LeadScoreboardService::class;

        $containerizedClasses[] = ResourcesPageService::class;

        return $containerizedClasses;
    }

    private function QueuesServices(array $containerizedClasses)
    {
        $containerizedClasses[] = SanitizationService::class;
        $containerizedClasses[] = AdminSanitization::class;
        $containerizedClasses[] = BaseSanitization::class;
        $containerizedClasses[] = NullSanitization::class;
        $containerizedClasses[] = SanitizationServiceFactory::class;
        $containerizedClasses[] = SanitizerSanitization::class;

        $containerizedClasses[] = AdminUnassigned::class;
        $containerizedClasses[] = BaseClientUnassigned::class;
        $containerizedClasses[] = GetClientUnassignedService::class;
        $containerizedClasses[] = GetClientUnassignedServiceFactory::class;
        $containerizedClasses[] = ManagingUnassigned::class;
        $containerizedClasses[] = NullUnassigned::class;

        return $containerizedClasses;
    }

    private function ShippingServices(array $containerizedClasses)
    {
        $containerizedClasses[] = NewShippingService::class;
        $containerizedClasses[] = NewShippingServiceFactory::class;
        $containerizedClasses[] = NullnewShipping::class;
        $containerizedClasses[] = NewShipping::class;

        $containerizedClasses[] = SingleShippingService::class;
        $containerizedClasses[] = SingleShippingServiceFactory::class;
        $containerizedClasses[] = NullSingleShipping::class;
        $containerizedClasses[] = SingleShipping::class;

        $containerizedClasses[] = DeleteShippingService::class;
        $containerizedClasses[] = DeleteShippingServiceFactory::class;
        $containerizedClasses[] = DeleteShipping::class;
        $containerizedClasses[] = NullDeleteShipping::class;

        $containerizedClasses[] = DeleteShippingClientsService::class;
        $containerizedClasses[] = DeleteShippingClientsServiceFactory::class;
        $containerizedClasses[] = DeleteShippingClients::class;
        $containerizedClasses[] = NullDeleteShippingClients::class;

        $containerizedClasses[] = UpdateShippingService::class;
        $containerizedClasses[] = UpdateShippingServiceFactory::class;
        $containerizedClasses[] = UpdateShipping::class;
        $containerizedClasses[] = NullUpdateShipping::class;

        return $containerizedClasses;
    }


    private function ContractorServices(array $containerizedClasses)
    {
        $containerizedClasses[] = OrphanedContractorService::class;
        $containerizedClasses[] = BaseOrphanedContractor::class;
        $containerizedClasses[] = ManagingPartnerOrphanedContractor::class;
        $containerizedClasses[] = NullOrphanedContractor::class;
        $containerizedClasses[] = OrphanedContractorServiceFactory::class;
        $containerizedClasses[] = OrphanedContractorSearchService::class;
        $containerizedClasses[] = OrphanedContractorSearchServiceFactory::class;
        $containerizedClasses[] = AdminOrphanContractorSearchService::class;
        $containerizedClasses[] = ManagingPartnerOrphanContractorSearchService::class;

        return $containerizedClasses;
    }

    private function ContractorPerformanceServices(array $containerizedClasses): array
    {
        $containerizedClasses[] = ContractorPerformanceService::class;
        $containerizedClasses[] = ContractorPerformanceReport::class;
        $containerizedClasses[] = ContractorPerformanceServiceFactory::class;
        return $containerizedClasses;
    }

    private function ClientsPerformanceServices(array $containerizedClasses): array
    {
        $containerizedClasses[] = ReportsPerformanceClientsService::class;
        $containerizedClasses[] = AdminReportsPerformanceClients::class;
        $containerizedClasses[] = NullReportsPerformanceClients::class;
        $containerizedClasses[] = ReportsPerformanceClientsServiceFactory::class;
        return $containerizedClasses;
    }

    private function WebhookServices(array $containerizedClasses)
    {
        $containerizedClasses[] = TypeFormWebhookService::class;
        $containerizedClasses[] = WebhookParser::class;

        $containerizedClasses[] = UpdateContractorUplineParentService::class;
        $containerizedClasses[] = BaseUpdateContractorUplineParentService::class;
        $containerizedClasses[] = NullUpdateContractorUplineParent::class;
        $containerizedClasses[] = UpdateContractorUplineParentServiceFactory::class;
        $containerizedClasses[] = ManagingPartnerUpdateContractorUplineParent::class;
        $containerizedClasses[] = UnsupportedUpdateContractorUplineParent::class;

        /* Calendly */
        $containerizedClasses[] = NewCalendlyLeadService::class;
        /* /Calendly */

        return $containerizedClasses;
    }

    public function withFiles(array $files): SuppTest {
        $this->request->method('getUploadedFiles')->willReturn($files);

        $files['uploaded_file']->getSize();
        return $this;
    }

    public function withPost(array $post): SuppTest {
        $this->request->method('getParsedBody')->willReturn($post);
        return $this;
    }



}
