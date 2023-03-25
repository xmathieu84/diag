<?php

namespace App\Helper;

use App\Repository\AgentRepository;

trait AgentRepoTrait{

    /**
     * @var AgentRepository
     *
     */
    protected AgentRepository $agentRepository;

    /**
     * @param AgentRepository $agentRepository
     * @required
     */
    public function setAgentRepo(AgentRepository $agentRepository):void{
        $this->agentRepository = $agentRepository;
    }
}
