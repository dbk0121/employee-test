<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\Plugin\Redirect;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Filter\File\RenameUpload;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;

class IndexController extends AbstractActionController
{
    private $adapter;
    public function  __construct(){
        $config = [
            'driver' => 'mysqli', 
            'database' => 'zenframe',
            'username' => 'root',
            'password' => '',
        ];
        $this->adapter = new Adapter($config);

        // $adapter = new Zend\Db\Adapter\Adapter(array(
        //     'driver' => 'Mysqli',
        //     'database' => 'zenframe',
        //     'username' => 'root',
        //     'password' => ''
        //  ));
    }
    public function indexAction()
    {
        $statement = $this->adapter->query("Select * from employee", Adapter::QUERY_MODE_EXECUTE); 
        $row = $statement->toArray();
        return new ViewModel(['result'=>$row]);
    }
    public function addAction()
    {
        $request = $this->getRequest();
        $your_name = isset($_POST['your_name']) ? $_POST['your_name'] : '';

        $filename = '';
        


        if($your_name){

            $getEmail = $_POST['your_email'];
            $sql = "Select * from employee where `email`='$getEmail'";
            $statement = $this->adapter->query("Select * from employee where `email`='$getEmail'", Adapter::QUERY_MODE_EXECUTE); 
            $row = $statement->toArray();
            if(count($row) > 0){
                return new ViewModel(['error'=>'Email already exits']);
            }else{
                if($request->isPost()){
                    $files =  $request->getFiles()->toArray();
                    $httpadapter = new \Zend\File\Transfer\Adapter\Http(); 
                    $filesize  = new \Zend\Validator\File\Size(array('min' => 1000 )); //1KB  
                    $extension = new \Zend\Validator\File\Extension(array('extension' => array('jpg','png')));
                    $httpadapter->setValidators(array($filesize, $extension), $files['file']['name']);

                    if($httpadapter->isValid()) {
                        // $httpadapter->setDestination('public/uploads/');
                        // if($httpadapter->receive($files['file']['name'])) {
                        //     $newfile = $httpadapter->getFileName();
                        //     $filename = $files['your_profile_img']['name'];
                        // }

                        $filename = time().'.jpg';
                        $httpadapter->addFilter('Rename', 'public/uploads/'.$filename,  $files['your_profile_img']['name']);
                        $httpadapter->receive($files['your_profile_img']['name']);
                        // $filename = $httpadapter->getFilename(); 
                    }
                }
            
                $sql = 'INSERT INTO employee (`name`, `address`, `email`, `phone`, `dbo`, `image`) VALUES ("'.$_POST['your_name'].'", "'.$_POST['address'].'", "'.$_POST['your_email'].'", "'.$_POST['your_phone'].'", "'.$_POST['your_dob'].'", "'.$filename.'")';
                $statement = $this->adapter->query($sql);
                $results = $statement->execute();
                
                return $this->redirect()->toRoute('home');
            }
        }
        else{
            return new ViewModel();
        }
        
    }
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id',0);
        $request = $this->getRequest();
        $your_name = isset($_POST['your_name']) ? $_POST['your_name'] : '';
        if($your_name){ 
            
                $files =  $request->getFiles()->toArray();
                $httpadapter = new \Zend\File\Transfer\Adapter\Http(); 
                $filesize  = new \Zend\Validator\File\Size(array('min' => 1000 )); //1KB  
                $extension = new \Zend\Validator\File\Extension(array('extension' => array('jpg','png')));
                $httpadapter->setValidators(array($filesize, $extension), $files['file']['name']);
                
                if($httpadapter->isValid()) {
                    echo 1;
                    // $httpadapter->setDestination('public/uploads/');
                    // if($httpadapter->receive($files['file']['name'])) {
                    //     $newfile = $httpadapter->getFileName();
                    //     $filename = $files['your_profile_img']['name'];
                    // }
                        
                    $filename = time().'.jpg';
                    $httpadapter->addFilter('Rename', 'public/uploads/'.$filename,  $files['your_profile_img']['name']);
                    $httpadapter->receive($files['your_profile_img']['name']);
                    // $filename = $httpadapter->getFilename(); 

                    $sql = "UPDATE `employee` SET `name`= '$_POST[your_name]',`address`='$_POST[address]', `phone`='$_POST[your_phone]', `dbo`='$_POST[your_dob]', `image`='$filename' WHERE `id`= $id";
                    $statement = $this->adapter->query($sql);
                    $results = $statement->execute();
                }else{
                    $sql = "UPDATE `employee` SET `name`= '$_POST[your_name]',`address`='$_POST[address]', `phone`='$_POST[your_phone]', `dbo`='$_POST[your_dob]' WHERE `id`= $id";
                    $statement = $this->adapter->query($sql);
                    $results = $statement->execute();
                }
            return $this->redirect()->toRoute('home');
        }
        else{ 
            $statement = $this->adapter->query("Select * from employee where id=$id", Adapter::QUERY_MODE_EXECUTE); 
            $row = $statement->current();
            return new ViewModel(['result'=>$row]);
        }
    }
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id',0);
        if($id){ 
            $sql = "DELETE FROM `employee` WHERE `id`=$id";
            $statement = $this->adapter->query($sql);
            $results = $statement->execute();
            return $this->redirect()->toRoute('home');
        }
    }
}
