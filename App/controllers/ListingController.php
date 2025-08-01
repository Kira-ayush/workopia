<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController
{
    protected $db;
    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }
    public function index()
    {

        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();

        loadView('listings/index', [
            'listings' => $listings
        ]);
    }
    public function create()
    {
        loadView('listings/create');
    }
    public function show($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        //Check if listing exists
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store data in database
     * 
     * @return void
     */

    public function store()
    {

        $allowedFields = ['title', 'description', 'salary', 'tags', 'requirements', 'benefits', 'company', 'address', 'city', 'state', 'phone', 'email'];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = 1;

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'salary', 'description', 'email', 'city', 'state'];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            //Reload view with errors
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            //submit data
            $fields = [];
            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }
            $fields = implode(',', $fields);

            $values = [];

            foreach ($newListingData as $field => $value) {
                //Convert empty strings to null
                if ($value === '') {
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;
            }
            $values = implode(',', $values);

            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
            $this->db->query($query, $newListingData);
            redirect(('/listings'));
        }
    }

    /**
     * Delete a listing
     * 
     * @param array $param
     * 
     * @return void
     * 
     */
    public function destroy($params)
    {
        $id = $params['id'];

        $param = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }
        $this->db->query('DELETE FROM listings WHERE id = :id', $params);

        //Set flash message
        $_SESSION['success_message'] = 'Listing deleted successfully';
        redirect('/listings');
    }
    /**
     * Show the listing edit form
     * 
     * @param array $param
     * 
     * @return void
     * 
     */
    public function edit($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        //Check if listing exists
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }
        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }
    /**
     * Update a listing
     * 
     * @param array $params
     * @return void
     */
    public function update($params){
        
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        //Check if listing exists
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }
        $allowedFields = ['title', 'description', 'salary', 'tags', 'requirements', 'benefits', 'company', 'address', 'city', 'state', 'phone', 'email'];

        $updateValues=[];
        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updateValues = array_map('sanitize',$updateValues);
        $requiredFields = ['title', 'salary', 'description', 'email', 'city', 'state'];

        $errors =[];

        foreach($requiredFields as $field){
            if(empty($updateValues[$field]) || !Validation::string($updateValues[$field])){
                $errors[$field] = ucfirst($field) . 'is required';
            }

        }
        if(!empty($errors)){
            loadView('listings/edit',[
                'listing'=> $listing,
                'errors'=>$errors
            ]);
            exit;
        }else{
            // Submit to database
            $updateFields = [];

            foreach(array_keys($updateValues) as $field){
                $updateFields[] = "{$field} = :{$field}";
            }
            $updateFields = implode(',', $updateFields);
            $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";

            $updateValues['id'] = $id;
            
            $this->db->query($updateQuery, $updateValues);
            $_SESSION['success_message'] = 'Listing Updated';
            redirect('/listings/' .$id);
        }

    }
}
