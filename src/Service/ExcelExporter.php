<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExporter
{
    public function exportData(string $dtype, array $headers, array $data, array $realHeaders = []): StreamedResponse
    {
        // Créer un nouvel objet Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Appliquer le style aux en-têtes (fond bleu foncé, texte en blanc)
        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10497D'], // Bleu foncé
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'], // Texte blanc
                'bold' => true,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'], // Bordure noire
                ]
            ]
        ];

        // Remplacer les en-têtes par les vrais en-têtes si fournies
        if (!empty($realHeaders)) {
            $headers = $realHeaders;
        }
        // Remplir les en-têtes dans la première ligne et appliquer le style
        foreach ($headers as $col => $header) {
            $column = chr(65 + $col); // Convertir l'index de colonne en lettre (A, B, C, etc.)
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->applyFromArray($headerStyle);
        }

        // Appliquer un style aux données (fond bleu clair)
        $dataStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'e0f0ff'], // Bleu clair
            ],
        ];
        $dataStyleBorders = [
            'borders' => [
                'inside' => [
                    'borderStyle' => Border::BORDER_HAIR, // Bordure très fine à l'intérieur
                    'color' => ['rgb' => '000000'], // Couleur noire
                ],
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Bordure plus épaisse autour
                    'color' => ['rgb' => '000000'], // Couleur noire
                ]
            ]
        ];

        // Remplir les données à partir de la ligne 2
        $row = 2;
        $letters = range('A', 'Z');
        $lastLetter = $letters[count($headers) - 1];
        foreach ($data as $item) {
            foreach ($headers as $key => $column) if (!is_object($item)) {
                $sheet->setCellValue($letters[$key] . $row, $item[$key]);
            }
            // Appliquer le style bleu clair aux cellules de données impaires
            if ($row % 2 !== 0) $sheet->getStyle("A{$row}:{$lastLetter}{$row}")->applyFromArray($dataStyle);
            $row++;
        }

        // Appliquer le style de bordure sur toute la plage de données
        $lastRow = $row - 1;
        $sheet->getStyle("A{$row}:{$lastLetter}{$lastRow}")->applyFromArray($dataStyleBorders);

        // Ajouter un filtre automatique sur la première ligne (les en-têtes)
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        // Ajuster automatiquement la taille des colonnes en fonction du contenu
        foreach (range('A', "{$lastLetter}") as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Création de la réponse streamée pour un téléchargement direct
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        // Définir les headers de la réponse pour forcer le téléchargement du fichier
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . uniqid($dtype . "_", true) . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
