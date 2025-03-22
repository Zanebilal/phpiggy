<?php 

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\config\Paths;

class ReceiptService
{
    public function __construct(private Database $db)
    {
    }

    ## validating files if exist
  public function validateFile(?array $file)
  {

    ## check if the file was uploaded successfully
    if(!$file || $file['error'] !== UPLOAD_ERR_OK){
        throw new ValidationException([
            'receipt' => ['Fialed to upload file']
        ]);
    }

    $maxFileSizaMb = 2 * 1024 * 1024;
    if($file['size'] > $maxFileSizaMb){
        throw new ValidationException([
            'receipt' => ['File upload is too large']
        ]);
    }

    $originalFileName = $file['name'];

    if(!preg_match('/^[A-Za-z0-9\s._-]+$/',$originalFileName)){
        throw new ValidationException([
            'receipt' => ['Invalid filename']
        ]);
    }

    $clientMimeType = $file['type'];
    $allowedMimeType = ['image/png', 'image/jpeg', 'application/pdf'];

    if(!in_array($clientMimeType, $allowedMimeType)){
        throw new ValidationException([
            'receipt' => ['Invalid file type']
        ]);
    }

  }

  public function upload(array $file, int $transaction_id)
  {
    $fileExtention = pathinfo($file['name'], PATHINFO_EXTENSION) ;

    ## generating rendom file name with the same extention of the original file
    $newFileName = bin2hex(random_bytes(16)) . "." . $fileExtention ;

    ## generating a full path to upload file to it
    $uploadPath = Paths::STORAGE_UPLOADS . "/" . $newFileName;

    ## check if the file has uploaded to the server successfully
    if(!move_uploaded_file($file['tmp_name'], $uploadPath)){
        throw new ValidationException([
            'receipt' => ['Failed to upload file']
        ]);
    }
    
    $this->db->query(
        "INSERT INTO receipts(transaction_id, original_filename, storage_filename, media_type)
        VALUES (:transaction_id, :original_filename, :storage_filename, :media_type)",
        [
            'transaction_id' => $transaction_id,
            'original_filename' => $file['name'],
            'storage_filename' => $newFileName,
            'media_type' => $file['type']
          ]);
  }

  public function getReceipt(string $id)
  {
    $receipt = $this->db->query(
        "SELECT * FROM receipts WHERE id = :id",
        [
            'id' => $id
        ]
     )->find();
     
    return $receipt;    
  }

  ## display the receipt content to the user
  public function read(array $receipt)
  {

    ## check if the file exist in our system (server) 

    $filePath = Paths::STORAGE_UPLOADS . '/' . $receipt['storage_filename'];

    if(!file_exists($filePath)){
        redirectTo('/');
    }

    ## confugering the headers to send the file to the browser and thier information
    ## using 'inline' to view the file content from within the browser or use 'attachment' to download the file
    header("Content-Disposition: inline;filename={$receipt['original_filename']}");
    header("Content-Type: {$receipt['media_type']}");

    ## send file with body
    readfile($filePath);

  }

  public function delete(array $receipt)
  {
    $filePath = Paths::STORAGE_UPLOADS . '/' . $receipt['storage_filename'];

    ## delete the receipt file from the server
    unlink($filePath);

    ## delete the receipt file from the database
    $this->db->query(
        "DELETE FROM receipts WHERE id = :id",
        [
            'id' => $receipt['id']
        ]
    );
  }

    
}