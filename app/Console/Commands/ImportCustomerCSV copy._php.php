<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;



class ImportCustomerCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv:customer {filePath? : CVS File path or URL}  {--truncateTable=0 : truncate table} {--auth=0 : HTTP authentication eg:username,password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import customer CSV data from Url';


    /**
     * The console command description.
     *
     * @var array
     */
    protected $csvHeader = [
        'ID',
        'Job Title',
        'Email Address',
        'FirstName LastName',
        'registered_since',
        'phone'
    ];

    /**
     * The console command description.
     *
     * @var array
     */
    protected $customerTableColumns = [
        'id',
        'job_title',
        'email',
        'full_name',
        'registered_since',
        'phone'
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    { 
        $file = $this->argument('filePath');
        $auth = [];
        if (filter_var($file, FILTER_VALIDATE_URL )) {
            if($this->option('auth')) {
                $auth = explode(',' , $this->option('auth'));
            }
            try {

                $file = $this->downloadCsvFileToTemp($file,$auth);
                /*

                $temp_file = tempnam(sys_get_temp_dir(), 'prefix');
                $client = new Client();
                $options =  ['stream' => true ];
                if(!empty($auth)){                    
                    $options = array_merge($options , ['auth' => $auth]);          
                }
                $response = $client->get($file,$options);
                $this->info("Fetching csv content ...");
                $this->newLine(1);
                $contents = $response->getBody();            
                while (!$contents->eof()) {
                    File::append($temp_file, $contents->read(1024));
                }
                $file = $temp_file;    
                */



            } catch (\Throwable $th) {
                $msg = "There was an error while accessing  the URL :  {$file} ";
                Log::error("ImportCustomerCSV : {$msg} ");
                Log::error($th);
                $this->components->error($msg);
                return 0;
            }        
        }

        Log::info('ImportCustomerCSV : Started importCustomerCsv Command with arguments' );
        if($this->option('truncateTable')){
            dd($this->option('truncateTable'));
            DB::table('customers')->truncate();
        } 
        
        $header = NULL;
        $data = [];
        $count = -1;
        $inserted = 0;
        if (file_exists($file) && is_readable($file)) 
        {
            if (($handle = fopen($file, 'r')) !== FALSE)
            {
                while (($row = fgetcsv($handle, null, ',')) !== FALSE) $count ++;                
                rewind($handle);  
                $this->newLine(2);
                $bar = $this->output->createProgressBar($count);
                $bar->setMessage('Task is in progress...');
                
                $bar->start();
                $bar->setMessage('Importing invoices...');
                while (($row = fgetcsv($handle, null, ',')) !== FALSE)
                {                
                    if(!$header){
                        $diff = array_diff($this->csvHeader, $row);
                        if (!empty($diff) && count($this->csvHeader) == count($row)) {
                            Log::error('ImportCustomerCSV : ncorrect csv format !!!');
                            break;
                        }                    
                        $header = $this->customerTableColumns;
                    }
                    else {        
                        $row = array_combine($header, $row);
                        $row['registered_since'] = $row['registered_since'] ? Carbon::createFromFormat('l,F j,Y', 
                            $row['registered_since']
                            )->toDateTimeString() : '';
                            
                        $data[] = $row;                   
                        if(count($data) >= 1000){     
                            if($this->insertToCustomerTable($data)) {
                                $bar->advance(1000);
                                $inserted  += 1000;
                            }
                            $data = [];                
                        }                    
                    }
                }
                fclose($handle);

                if(count($data) > 0) {
                    if($this->insertToCustomerTable($data)) {
                        $bar->advance(count($data));
                        $inserted  += count($data);
                    }
                    $data = [];
                }                
                $bar->finish();
            } else {
                $msg = 'Could not able to insert data to the table !';
                $this->components->error($msg);
                Log::error("ImportCustomerCSV : {$msg} ");
                return 0;
            }
        }
        else 
        {
            $msg = "Could find / open the CSV file {$file} ";
            $this->components->error($msg);
            Log::error("ImportCustomerCSV : {$msg} ");
            return 0;
        }
 
        $this->newLine(2);
        if($inserted != $count){ 
            $msg = "CSV Import is partially completed! Only {$inserted} records out of {$count} records were imported into the database";
            Log::error("ImportCustomerCSV : {$msg} ");
            $this->components->error($msg);
        }else{
            $msg = "CSV Import completed successfully!  {$inserted} records out of {$count} records were imported into the database";
            Log::info("ImportCustomerCSV : {$msg} ");
            $this->components->info($msg);
        }    
        return Command::SUCCESS; 
    }

    private function insertToCustomerTable($data) {
        $success = false;
        try {
            DB::table('customers')->upsert($data,
                ['id'],
                ['job_title',
                'email',
                'full_name',
                'registered_since',
                'phone']);  
            $success = true;
        } catch (\Exception $exception) { 
            Log::error('ImportCustomerCSV : Could not able to insert data to the table !');
            Log::error($exception);
        }   
        return $success;
    }
    
    private function downloadCsvFileToTemp($file, $auth) {
        
        $temp_file = tempnam(sys_get_temp_dir(), 'prefix');
        $client = new Client();
        $options =  ['stream' => true ];
        if(!empty($auth)){                    
            $options = array_merge($options , ['auth' => $auth]);          
        }
        $response = $client->get($file,$options);
        $this->info("Fetching csv content ...");
        $this->newLine(1);
        $contents = $response->getBody();            
        while (!$contents->eof()) {
            File::append($temp_file, $contents->read(1024));
        }
        $file = $temp_file;   
        
        return $file;

    }
}