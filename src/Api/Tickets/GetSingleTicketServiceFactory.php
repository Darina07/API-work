<?php

namespace Supp\Api\Tickets;

use Erc\Api\Shipments\NullUpdateShipping;
use Erc\Api\Shipments\UpdateShipping;
use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;

class GetSingleTicketServiceFactory
{
    public static function getService(Container $container, $ticketID) {
        $ticket_owner = self::getTicketOwner($container, $ticketID);
        switch($container->get('current_user')->role) {
            case 1: // Admin
            case 2: // Super Admin
                return $container->get(AdminSingleTicket::class);
            default:
                if ($ticket_owner == 1) {
                    // ticket is of this user / created_by this user
                    return $container->get(CurrentUserSingleTicket::class);
                } else{
                    return $container->get(OtherRolesSingleTicket::class);
                }
        }
    }

    /**
     * @param \League\Container\Container $container
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private static function getTicketOwner(Container $container, $ticketID)
    {
        $runner = $container->get(QueryRunner::class);
        $sql = "select created_by from tickets where id = :ticket_id";
        $values = ['ticket_id' => $ticketID];
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result['created_by'] == $container->get('current_user')->id){
            return 1;
        } else{
            return 0;
        }
    }
}