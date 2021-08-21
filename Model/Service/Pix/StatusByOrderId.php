<?php

namespace FCamara\Getnet\Model\Service\Pix;

use Magento\Sales\Api\OrderRepositoryInterface;
use FCamara\Getnet\Api\StatusByOrderIdInterface;

class StatusByOrderId implements StatusByOrderIdInterface
{

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepositoryInterface;

    /**
     * StatusByOrderId constructor.
     * @param OrderRepositoryInterface $orderRepositoryInterface
     */
    public function __construct(
        OrderRepositoryInterface $orderRepositoryInterface
    ) {
        $this->orderRepositoryInterface = $orderRepositoryInterface;
    }

    /**
     * @param int $id
     * @return mixed|string|null
     */
    public function execute(int $id)
    {
        $order = $this->orderRepositoryInterface->get($id);
        return $order->getStatus();
    }

}
