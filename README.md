This Flow package extends the [Neos Swift Mailer](https://github.com/neos/swiftmailer) by a spool for asynchronous mailing.

## Configuration

The package is pre-configured to use a the file spool:

	WE:
	  SwiftMailerSpool:
		spool:
		  type: 'Swift_FileSpool'
		  options: []
		  arguments:
			path: %FLOW_PATH_DATA%/SwiftMailerSpool/
			
## Usage

Different from the [Neos Swift Mailer](https://github.com/neos/swiftmailer), a `\Swift_Message` has to be created instead 
of using the `\TYPO3\SwiftMailer\Message` object. This is due to a problems on the serialization of the message that 
happens in the `\FileSpool` as the serialized `\TYPO3\SwiftMailer\Message` does not include its parent properties of the 
`\Swift_Message`. 

The process is the same as with the normal SwiftMailer library.

Inject the mailer interface which now is a `\Swift_SpoolTransport` object:

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SwiftMailer\MailerInterface
	 */
	protected $mailer;

Create the message:

	$mail = new \Swift_Message();

Send the message with the mailer:

	$this->mailer->send($mail);

Now, the mail is in the spool and can really be sent by the command:

	./flow swiftmailerspool:flush

