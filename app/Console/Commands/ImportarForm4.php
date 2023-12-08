<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Models\Formulario;
use App\Models\Propiedad;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class ImportarForm4 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importar:form4 {--C|cik=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar form4 de una empresa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cik = $this->option('cik');
        $empresa = Empresa::where('cik', $cik)->first();
        $companyDataFile = $this->downloadCompanyData($cik);
        $companyData = json_decode($companyDataFile);
        $recentForms = $companyData->filings->recent->form;

        foreach ($recentForms as $index => $form) {
            $accessionNumber = $companyData->filings->recent->accessionNumber[$index];
            $fileName = $companyData->filings->recent->primaryDocument[$index];
            $existForm = Formulario::where('codigo', $accessionNumber)->get();

            if ($existForm->count() > 0) {
                continue;
            }

            if ($form == '4') {
                $this->proccessForm4($cik, $empresa, $fileName, $accessionNumber);
            }
        }

        $oldFormsList = $companyData->filings->files;

        foreach ($oldFormsList as $form) {
            $oldForms = json_decode($this->downloadOldFormsData($form->name));

            $accessionNumber = $oldForms->accessionNumber[$index];
            $fileName = $oldForms->primaryDocument[$index];
            $existForm = Formulario::where('codigo', $accessionNumber)->get();

            if ($existForm->count() > 0) {
                continue;
            }

            foreach ($oldForms->form as $index => $form) {
                if ($form == '4') {
                    $this->proccessForm4($cik, $empresa, $fileName, $accessionNumber);
                }
            }

        }



        return 0;
    }

    private function proccessForm4($cik, $empresa, $fileName, $accessionNumber) {
        $fileName = $this->getFileName($fileName);
        $form4Xml = $this->downloadForm4($fileName, $cik, $accessionNumber);
        $parsedForm = simplexml_load_string($form4Xml);
        $jsonForm = $this->convertXmlToJson($parsedForm);
        $this->saveForm('4', $accessionNumber, $empresa->id, $jsonForm);
    }

    private function getFileName($filename) {
        $filename = explode('/', $filename);

        if (count($filename) > 1) {
            $filename = $filename[count($filename) - 1];
        }

        return $filename;
    }

    private function convertXmlToJson($xml) {
        $json = json_encode($xml);
        $json = json_decode($json, true);
        return $json;
    }

    private function saveForm($tipo, $codigo, $empresa, $form) {
        $formulario = new Formulario;
        $formulario->tipo = $tipo;
        $formulario->codigo = $codigo;
        $formulario->empresa_id = $empresa;
        $formulario->formulario = $form;
        $formulario->save();

        return $formulario;
    }

    private function downloadOldFormsData($file) {
        $jsonUrl = "https://data.sec.gov/submissions/{$file}";
        $file = Http::withUserAgent('Name (fborquez@outlook.com)')->get($jsonUrl);
        usleep(500000);
        return $file->body();
    }

    private function downloadCompanyData($cik) {
        $paddedCik = str_pad($cik, 10, '0', STR_PAD_LEFT);
        $jsonUrl = "https://data.sec.gov/submissions/CIK{$paddedCik}.json";
        $file = Http::withUserAgent('Name (fborquez@outlook.com)')->get($jsonUrl);
        usleep(500000);
        return $file->body();
    }

    private function downloadForm4($fileName, $cik, $code) {
        $replacedCode = str_replace('-', '', $code);
        $jsonUrl = "https://www.sec.gov/Archives/edgar/data/{$cik}/{$replacedCode}/{$fileName}";
        $file = Http::withUserAgent('Name (fborquez@outlook.com)')->get($jsonUrl);
        usleep(500000);
        return $file->body();
    }
}
