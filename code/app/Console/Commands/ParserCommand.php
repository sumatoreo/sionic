<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\ProgressBar;

class ParserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $files = Storage::files('data');
        $xmlReader = new \XMLReader();
        $datum = [];

        foreach ($files as $file) {
            if (preg_match('/(\w+)(\d_\d).xml/', $file, $match)) {
                $datum[$match[2]][$match[1]] = $file;
            }
        }

        foreach ($datum as $key => $item) {
            $file = storage_path('app/' . $item['import']);
            $this->info($file);
            $this->importParser($file, $xmlReader);
            echo PHP_EOL . PHP_EOL;
        }

        foreach ($datum as $key => $item) {
            $file = storage_path('app/' . $item['offers']);
            $this->info($file);
            $this->offerParser($file, $xmlReader);
            echo PHP_EOL . PHP_EOL;
        }
    }

    /**
     * Parsing offers file
     *
     * @param string $file
     * @param \XMLReader $xmlReader
     */
    private function offerParser(string $file, \XMLReader $xmlReader)
    {
        $xmlReader->open($file);
        $city = null;

        while ($xmlReader->read()) {
            if ($xmlReader->nodeType === \XMLReader::ELEMENT) {
                if ($xmlReader->depth === 2 && $xmlReader->name === 'Ид' && empty($city)) {
                    $city = $xmlReader->readString();
                }

                if ($xmlReader->name === 'Предложения' && $xmlReader->depth === 2) {
                    $this->initBar($xmlReader, $bar);
                }

                if ($xmlReader->name === 'Предложение' && $xmlReader->depth === 3) {
                    $data = $this->xmlToAray($xmlReader);
                    $this->scanProduct($data, $city);
                    $bar->advance();
                }
            }
        }

        $bar->finish();
        $xmlReader->close();
    }

    /**
     * Parsing import file
     *
     * @param string $file
     * @param \XMLReader $xmlReader
     */
    private function importParser(string $file, \XMLReader $xmlReader)
    {
        $xmlReader->open($file);
        $city = [];
        $count = 0;
        $bar = null;

        while ($xmlReader->read()) {
            if ($xmlReader->nodeType === \XMLReader::ELEMENT) {
                if ($xmlReader->depth === 2 && ($xmlReader->name === 'Ид' || $xmlReader->name === 'Наименование')) {
                    $count++;

                    if ($count > 2) {
                        $city[$xmlReader->name] = $xmlReader->readString();
                    }

                    if ($count > 3) {
                        $this->saveCity($city);
                    }
                }

                if ($xmlReader->depth === 2 && $xmlReader->name === 'Товары') {
                    $this->initBar($xmlReader, $bar);
                }

                if ($xmlReader->name === 'Товар' && $xmlReader->depth === 3) {
                    $data = $this->xmlToAray($xmlReader);
                    $this->saveProduct($data);
                    $bar->advance();
                }
            }
        }

        $bar->finish();
        $xmlReader->close();
    }

    /**
     * Save City
     *
     * @param array $data
     */
    private function saveCity(array $data)
    {
        try {
            DB::table('cities')
                ->insertOrIgnore([
                    'id' => trim($data['Ид']),
                    'name' => trim($data['Наименование'])
                ]);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Scan Product
     *
     * @param array $data
     * @param string $city
     */
    private function scanProduct(array $data, string $city)
    {
        $product = DB::table('products')
            ->where('code', $data['Код'])
            ->first();

        $price = 0;

        if (isset($data['Цены']['Цена'])) {
            $price = $data['Цены']['Цена'];

            if (isset($price['Представление'])) {
                $price = $price['ЦенаЗаЕдиницу'];
            } else {
                $price = $price[0]['ЦенаЗаЕдиницу'];
            }
        }

        try {
            if ($product) {
                DB::table('cities_products')
                    ->updateOrInsert([
                        'city_id' => trim($city),
                        'product_id' => $product->id
                    ], [
                        'count' => intval(trim($data['Количество'])),
                        'price' => floatval(trim($price))
                    ]);
            } else {
                $this->error('Нет кода: ' . $data['Код']);
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Save Product
     *
     * @param array $data
     */
    private function saveProduct(array $data)
    {
        $json = '[]';

        if (isset($data['Взаимозаменяемости']['Взаимозаменяемость'])) {
            $res = $data['Взаимозаменяемости']['Взаимозаменяемость'];

            if (isset($res['Модель'])) {
                $res = [$res];
            }

            $json = json_encode($res, JSON_UNESCAPED_UNICODE);
        }

        try {
            DB::table('products')
                ->updateOrInsert([
                    'code' => trim($data['Код'])
                ], [
                    'name' => trim($data['Наименование']),
                    'weight' => trim($data['Вес']),
                    'usage' => $json
                ]);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * XML to Array
     *
     * @param \XMLReader $xmlReader
     * @return array
     */
    private function xmlToAray(\XMLReader $xmlReader): array
    {
        $xml = simplexml_load_string($xmlReader->readOuterXml());
        $json = json_encode($xml);
        $array = json_decode($json, true);

        return $array;
    }

    /**
     * Init Bar
     *
     * @param \XMLReader $xmlReader
     * @param $bar
     */
    private function initBar(\XMLReader $xmlReader, &$bar)
    {
        $elem = new \SimpleXMLElement($xmlReader->readOuterXml());
        $bar = $this->output->createProgressBar($elem->count());
        $bar->setFormat('debug');
        $bar->start();
    }
}
