<?php

require_once('class.Link.php');

class LinkUse extends Link {

    function LinkUse($entity1_id, $entity1_type, $entity2_id) {
        $this->link_type = 'use';

        parent::Link($entity1_id, $entity1_type, $entity2_id, 'vendor', $this->link_type);
    }

}

?>