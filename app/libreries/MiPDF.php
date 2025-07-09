<?php

class MiPDF extends TCPDF
{
    public function Header()
    {
        // Ruta al logo (ajusta la ruta según tu proyecto)
        $logo = __DIR__ . '/../../public/images/resources/OscarCapital-Logo.png';
        $this->Image($logo, 20, 10, 40, '', 'PNG');
        // Línea debajo del header
        $this->SetLineStyle(['width' => 0.3]);
        $this->Line(20, 30, 190, 30);
        $this->Ln(10);
    }

    // Pie de página con numeración
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 
            'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(),
            0, false, 'C'
        );
    }
}
?>