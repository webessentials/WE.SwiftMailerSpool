<?php
namespace WE\SwiftMailerSpool;

/*                                                                        *
 * This script belongs to the Flow package "SwiftMailerSpool".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\ObjectAccess;

/**
 * Spool transport factory for the SwiftMailerSpool package
 *
 * @author Simon Gadient <simon@web-essentials.asia>
 */
class SpoolTransportFactory {

	/**
	 * @Flow\Inject
	 * @var \Neos\SwiftMailer\TransportFactory
	 */
	protected $transportFactory;

	/**
	 * Factory method which create a spool transport with the given options.
	 *
	 * @param string $spoolType Object name of the spool to be used
	 * @param array $spoolOptions Options for the spool
	 * @param array $spoolArguments Constructor arguments for the spool
	 * @return \WE\SwiftMailerSpool\SpoolTransportInterface The created spool transport instance
	 * @throws Exception
	 */
	public function create($spoolType, array $spoolOptions = array(), array $spoolArguments = NULL) {
		if (!class_exists($spoolType)) {
			throw new Exception('The specified spool "' . $spoolType . '" does not exist.', 1269351207);
		}

		if (is_array($spoolArguments)) {
			$class = new \ReflectionClass($spoolType);
			$spool =  $class->newInstanceArgs($spoolArguments);
		} else {
			$spool = new $spoolType();
		}
		foreach ($spoolOptions as $optionName => $optionValue) {
			if (ObjectAccess::isPropertySettable($spool, $optionName)) {
				ObjectAccess::setProperty($spool, $optionName, $optionValue);
			}
		}

		$transport = $this->transportFactory->create('Swift_SpoolTransport', array(), array('spool' => $spool));

		return $transport;
	}

}
