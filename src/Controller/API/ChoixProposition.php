<?php


namespace App\Controller\API;


use App\Entity\Intervention;
use App\Helper\EntityManagerTrait;
use App\Repository\PropositionRepository;
use App\Service\PropChoix;



class ChoixProposition
{
    use EntityManagerTrait;
    private PropositionRepository $propositionRepository;

    private PropChoix $propChoix;
    public function __construct(PropositionRepository $propositionRepository,PropChoix $propChoix)
    {
        $this->propositionRepository=$propositionRepository;
        $this->propChoix=$propChoix;
    }

    public function __invoke(Intervention $data)
    {
        $proposition = $this->propositionRepository->findOneBy(['id'=>$data->prop]);
        $this->propChoix->traitementProposition($data,$proposition);
        $this->manager->flush();
        exit();
    }
}