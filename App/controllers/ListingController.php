<?php

namespace App\Controllers;

use Error;
use Framework\Database;
use Framework\Validation;

class ListingController{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show all listings
     * 
     * @return void
     */

    public function index(){

        $listings = $this->db->query("SELECT * FROM listings")->fetchAll();

        loadView('listings/index', [
            'listings' => $listings
        ]);
    }


    /**
     * Show the create listing form
     *
     * @return void
     */
    public function create(){
        loadView('listings/create');
    }


    /**
     * Show a single listing
     * @param array $params
     * @return void
     */
    public function show($params){
        $id = $params['id'] ?? '';
        $params = [
            'id' => $id
        ];

        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

        // Check if listing exists
        if(!$listing){
            ErrorController::notFound('Listing not found.');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store data in database
     * @return void
     */
    public function store(){
        $allowedFields = [
            'title',
            'description',
            'salary',
            'requirements',
            'benefits',
            'company',
            'address',
            'city',
            'state',
            'phone',
            'email',
            'tags'
        ];
        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = 2;

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'email', 'city', 'state', 'salary'];

        $errors = [];

        foreach($requiredFields as $field){
            if(empty($newListingData[$field]) || !Validation::string($newListingData[$field])){
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }
        
        if(!empty($errors)){
            // Reload view with errors
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        }else{
            // Submit data

            // creating this query
            /*"INSERT INTO listings (title, description, salary, requirements, benefits, company, address, city, state, phone, email, user_id 
            VALUES (:title, :description, :salary, :requirements, :benefits, :company, :address, :city, :state, :phone, :email, :user_id))*/
            $fields = [];
            foreach($newListingData as $field => $value){
                $fields[] = $field;
            }

            $fields = implode(', ', $fields);
            
            $values = [];

            foreach($newListingData as $field => $value){
                // convert empty strings to null
                if($value === ''){
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;
            }

            $values = implode(', ', $values);
            
            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

            $this->db->query($query, $newListingData);
            redirect('/listings');

        }
}

    /**
     * Delete listing
     * @param array $params
     * @return void
     */
    public function destroy($params){
        $id = $params['id'];

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if(!$listing){
            ErrorController::notFound('Listing not found.');
            return;
        }
        
        $this->db->query('DELETE FROM listings WHERE id = :id', $params);

        // Set flash message
        $_SESSION['success_message'] = 'Listing deleted successfully';

        redirect('/listings');
    }

     /**
     * Update the listing edit form
     * @param array $params
     * @return void
     */
    public function edit($params){
        $id = $params['id'] ?? '';
        $params = [
            'id' => $id
        ];

        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

        // Check if listing exists
        if(!$listing){
            ErrorController::notFound('Listing not found.');
            return;
        }

        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }
}