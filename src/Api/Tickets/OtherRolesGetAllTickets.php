<?php

namespace Supp\Api\Tickets;

class OtherRolesGetAllTickets extends BaseAllTickets
{
  public function getSql(): string
  {

    return <<<HEREDOC
select t.id,
     t.title,
     tt.name as ticket_type,
     t.ticket_type as type,
     tc.name as ticket_category,
     t.category,
     t.created_by,
     t.assignee,
     t.created_on
from tickets t
join ticket_types tt on tt.id = t.ticket_type
join ticket_categories tc on tc.id = t.category
where t.created_by = :user_id
HEREDOC;
  }

  public function getValues():array {
    return [
        'user_id' => $this->container->get('current_user')->id
    ];

  }

}
