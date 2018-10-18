<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\IAuditor;
use Adbar\Dot;

class Unit extends TestCase
{
    private $config,$class;

    public function setUp(){
        // Config
        $this->config   =   require \hiqdev\composer\config\Builder::path('api');
        // Client
        $this->class    =   new IAuditor\IAuditor(API_KEY,$this->config);        
        // API_KEY constant is set
        $this->assertTrue(defined('API_KEY'));
        // API_KEY is not null
        $this->assertNotNull(API_KEY);
        // API_KEY is not empty
        $this->assertNotEmpty(API_KEY);
    }
    
    /**
     * @test
     * @covers \BlackQuadrant\IAuditor\IAuditor::resource_splitter
     * @dataProvider resource_splitter
     */
    public function resourceSplitter($payload, $expected_op){
        $op     =   $this->class->resource_splitter($payload);
        $this->assertSame($expected_op, $op);
    }
    public function resource_splitter(){
        return [
            [
                'payload'   =>  'audit',
                'eop'       =>  [['audit'],'list']
            ]
        ];
    }
    /**
     * @test
     * @covers \BlackQuadrant\IAuditor\IAuditor::request_builder
     * @dataProvider request_builder
     */
    public function requestBuilder($resource,$filter,$eop){
        list($resource,$action) =   $resource;
        $op     =   $this->class->request_builder($resource,$action,$filter);
        $this->assertEquals($eop,$op);
    }
    public function request_builder(){
        $root     =   'https://api.safetyculture.io/';
        return [
            [
                'resource'  =>  [['audit'],'list'],
                'filter'    =>  [],
                'eop'  =>  [
                    'GET',
                    $root.'audits/search',
                    [
                        'query' =>  [
                            'order'     =>  'desc',
                            'limit'     =>  1000
                        ]
                    ]
                ]
            ]
        ];
    }
    /**
     * @test
     * @covers \BlackQuadrant\IAuditor\IAuditor::call
     * @dataProvider call_data
     */
    public function call($resource,$filter,$success){
        if(empty($filter)){
            $response   =   $this->class->call($resource);
        }
        else{
            $response   =   $this->class->call($resource,$filter);
        }
        if(strpos($resource,':')!==false){    
            $response   =   [$response];
        }
        $this->assertTrue($this->check_success($response,$success));
    }
    public function call_data(){
        return [
            [
                'resource'  =>  'audit',
                'filter'    =>  [],
                'success'   =>  [
                    'audit_id' => '__EXISTS__'
                ]
            ],
            [
                'resource'  =>  'audit:audit_a291a59f902b4bca98e780d3a6a1055d',
                'filter'    =>  [],
                'success'   =>  [
                    'audit_data.score'    =>  '__EXISTS__'
                ]
            ]           
        ];
    }
    
    private function check_success($response,$success){
        foreach($response as $record){
            $record     =   dot($record);
            foreach($success as $key=>$value){
                if(strpos($value,'__')!==false){
                    if($value==='__EXISTS__'){
                        if(!$record->has($key)){
                            return false;
                        }
                    }
                }
                else{
                    if($record->get($key)!=$value){
                        return false;
                    } 
                }                
            }
        }
        return true;
    }
}

?>