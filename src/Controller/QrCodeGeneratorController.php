<?php
 
namespace App\Controller;

use Endroid\QrCode\Builder\Builder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;

#[Route('/{_locale}')]
class QrCodeGeneratorController extends AbstractController
{
    #[Route('/qr-codes', name: 'app_qr_codes')]
    public function index(): Response
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data('fb://profile/123456789')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Quartile)
            ->size(100)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->logoPath('assets/images/adrar_epa_logo_w_bg-5711b70db1d30a73d629b99c345bf989.png')
            ->logoResizeToWidth(30)
            ->logoPunchoutBackground(true)
            ->labelText('')
            ->labelFont(new NotoSans(20))
            ->labelAlignment(LabelAlignment::Center)
            ->validateResult(false)
            ->build();
        
        return $this->render('qr_code_generator/index.html.twig', [
            'qrCode' => $result->getDataUri()
        ]);
    }
}