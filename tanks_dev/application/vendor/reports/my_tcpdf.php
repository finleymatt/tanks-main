<?php
/************************************************************************
 * This file creatis a custom MY_TCPDF class which overrides TCPDF
 * class to include page number in the footer.
 *
 * Used for reports.
 ************************************************************************/

if (! class_exists('MY_TCPDF')) {

	//require_once('tcpdf_old/tcpdf.php');

	class MY_TCPDF extends TCPDF {
		public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) {
			parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
			$this->setPrintHeader(TRUE);
			$this->setPrintFooter(TRUE);
			$this->SetMargins(40, 40, 40);  // left, top, right
		}

		public function Header() {
		}

		public function Footer() {
			$this->SetXY(35, -45); // left, bottom
			$this->SetFont('helvetica', '', 8);
			$this->Cell(0, 8, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 1, 'L', 0, '', 0, false, 'M', 'M');
		}
	}

}

?>
