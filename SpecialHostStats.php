<?php
/**
 * The file that implements Special:HostStats.
 * 
 * @file
 * @ingroup Extensions
 */

class SpecialHostStats extends SpecialPage {
	protected $cmdwhitelist;

	public function __construct() {
		parent::__construct( 'HostStats' );
	}

	public function execute( $par ) {
		global $wgHostStatsCommands;
		$this->setHeaders();
		$this->whitelistedcmds();
		$commands = array();
		foreach ( $wgHostStatsCommands as $cmd ) {
			if ( in_array( $cmd, $this->cmdwhitelist ) ) {
				array_push( $cmd, $commands );
			} else {
				# Reject those unsafe commands and log it to hoststats
				wfDebugLog( "hoststats", "Rejected running command '" . 
					$cmd . "' as it is unsafe, please remove it from " . 
					"\$wgHostStatsCommands!" );
				continue;
			}
		}
		$this->getOutput->setPageTitle( wfMessage( 'hoststats-title' )->escaped() );
		$outpage = wfMessage( 'hoststats-intro' )->escaped();
		$outpage .= "\n";
		foreach ( $commands as $cmd ) {
			$outpage .= '<h3>' . $cmd . '</h3>';
			$outpage .= "\n<pre>\n" . $this->query( $cmd ) . "</pre>";
		}
		$this->getOutput->addWikiText( $outpage );
	}

	protected function query( $query ) {
		$output = wfShellExec( $query );
		return $output;
	}

	protected function whitelistedcmds() {
		$this->cmdwhitelist = array(
			'df',
			'whoami',
			'hostname',
		);
	}
}
