<?php

namespace FCamara\Getnet\Model\Service\Pix;

use FCamara\Getnet\Api\ServiceNotificationPixInterface;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Webapi\Rest\Request;
use Psr\Log\LoggerInterface;

class ServiceNotificationPix implements ServiceNotificationPixInterface
{

    private $orderRepositoryInterface;

    private $logger;

    private $request;

    public function __construct(
        OrderRepositoryInterface $orderRepositoryInterface,
        LoggerInterface $logger,
        Request $request
    ) {
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->logger = $logger;
        $this->request = $request;
    }

    public function execute()
    {
        try {
            $data = $this->request->getParams();
            $order = $this->orderRepositoryInterface->get($data['order_id']);

            if ($data['status'] == "APPROVED") {
                $order->setStatus("complete");

                $history = "Payment Type " . $data['payment_type'] . "\n";
                $history .= "Payment Id " . $data['payment_id'] . "\n";
                $history .= "Transaction Id " . $data['transaction_id'] . "\n";
                $history .= "Transanction Timestamp " . $data['transaction_timestamp'] . "\n";
                $history .= "Receiver PSP Name " . $data['receiver_psp_name'] . "\n";
                $history .= "Receiver Name " . $data['receiver_name'] . "\n";
                $history .= "Receiver CNPJ " . $data['receiver_cnpj'] . "\n";
                $history .= "Receiver CPF " . $data['receiver_cpf'] . "\n";
                $history .= "Terminal NSU " . $data['terminal_nsu'] . "\n";

                $order->addCommentToStatusHistory($history);
            }

            $this->orderRepositoryInterface->save($order);


        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        }

    }
}
