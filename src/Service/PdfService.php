<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfService
{
    private Dompdf $domPdf;

    public function __construct(private readonly Environment $twig)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);

        $this->domPdf = new Dompdf($pdfOptions);
    }

    /**
     * Génère un fichier PDF à partir d'un template Twig.
     *
     * @param string $template Twig template path
     * @param array $data Données à passer au template
     * @param string $filename Nom du fichier téléchargé
     */
    public function generatePdfFromTwig(string $template, array $data, string $filename): void
    {
        $html = $this->twig->render($template, $data);
        
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4', 'portrait');
        $this->domPdf->render();
        
        $this->domPdf->stream($filename, [
            "Attachment" => true
        ]);
    }

    /**
     * Retourne le contenu brut du PDF
     */
    public function outputPdfFromTwig(string $template, array $data): string
    {
        $html = $this->twig->render($template, $data);
        
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4', 'portrait');
        $this->domPdf->render();
        
        return $this->domPdf->output();
    }
}
