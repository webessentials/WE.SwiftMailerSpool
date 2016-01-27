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
	 * Flush the email spool queue
	 * 
	 * @throws \TYPO3\SwiftMailer\Exception
	 */
	public function flushCommand() {
		$settings = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.SwiftMailer');
		$realTransport = $this->transportFactory->create($settings['transport']['type'], $settings['transport']['options'], $settings['transport']['arguments']);
		/** @var \Swift_Spool $spool */
		$spool = $this->spoolMailer->getTransport()->getSpool();
		$sent = $spool->flushQueue($realTransport);
		$this->outputLine($sent . ' mails sent');
	}

}
