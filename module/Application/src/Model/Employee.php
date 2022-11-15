<?php  
namespace Tutorial\Model;  
use Zend\Db\TableGateway\TableGatewayInterface;  


class Employee { 
   public $id; 
   public $name; 
   public $address;
   public $email;
   public $phone;
   public $dbo; 
   public $image;
}



class EmployeeTable {
   protected $tableGateway; 
   public function __construct(TableGatewayInterface $tableGateway) { 
      $this->tableGateway = $tableGateway; 
   }  
   public function fetchAll() { 
      $resultSet = $this->tableGateway->select(); 
      return $resultSet; 
   } 
}