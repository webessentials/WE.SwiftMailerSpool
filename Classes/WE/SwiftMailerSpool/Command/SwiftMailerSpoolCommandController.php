<?php
namespace WE\SwiftMailerSpool\Command;

/*                                                                        *
 * This script belongs to the Flow package "SwiftMailerSpool".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;

/**
 * Spool Command Controller for the SwiftMailerSpool package
 *
 * @author Simon Gadient <simon@web-essentials.asia>
 */
class SwiftMailerSpoolCommandController extends CommandController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SwiftMailer\MailerInterface
	 */
	protected $spoolMailer;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SwiftMailer\TransportFactory
	 */
	protected $transportFactory;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @internal
	 */
	protected $configurationManager;

	/**
	 * @Flow\Inject
	 * @var \WE\SwiftMailerSpool\Log\SwiftMailerSpoolLoggerInterface
	 */
	protected $logger;

	/**
	 * Flush the email spool queue
	 * 
	 * @throws \TYPO3\SwiftMailer\Exception
	 */
	public function flushCommand() {
		$swiftMailerSettings = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.SwiftMailer');
		$realTransport = $this->transportFactory->create($swiftMailerSettings['transport']['type'], $swiftMailerSettings['transport']['options'], $swiftMailerSettings['transport']['arguments']);
		$swiftMailerSpoolSettings = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'WE.SwiftMailerSpool');
		/** @var \Swift_ConfigurableSpool $spool */
		$spool = $this->spoolMailer->getTransport()->getSpool();
		if (is_int($swiftMailerSpoolSettings['spool']['timeLimit'])) {
			$spool->setTimeLimit($swiftMailerSpoolSettings['spool']['timeLimit']);
		}
		if (is_int($swiftMailerSpoolSettings['spool']['messageLimit'])) {
			$spool->setMessageLimit($swiftMailerSpoolSettings['spool']['messageLimit']);
		}
		$failedRecipients = array();
		$sent = $spool->flushQueue($realTransport, $failedRecipients);
		$this->logger->log($sent . ' mails sent.');
		$count = count($failedRecipients);
		if ($count > 0) {
			$this->logger->log($count . ' recipients failed. Check the spool to see the messages.', LOG_WARNING);
		}
	}

}
