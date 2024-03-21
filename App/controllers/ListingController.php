<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController {
    protected $db;
    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index() {
        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();

        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create() {
        loadView('listings/create');
    }

    public function show($params) {
        $id = $params['id'] ?? '';

$params = [
    'id' => $id
];

$listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params )->fetch();

if(!$listing) {
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
    public function store() {
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));
        
        $newListingData['user_id'] = 1;

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'email', 'city', 'state'];

        $errors = [];

        foreach($requiredFields as $field) {
            if(empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

      if(!empty($errors)) {
        // Reaload view with errors
        loadView('listings/create', [
            'errors' => $errors,
            'listing' => $newListingData
        ]);

      } else {
        // Submit data
        $fields = [];

        foreach($newListingData as $field => $value) {
            $fields[] = $field;
        }

        $fields = implode(', ', $fields);

        $values = [];

        foreach($newListingData as $field => $value) {
            if($value === '') {
                $newListingData[$field] = null; 
            }
            $values[] = ':' . $field;
        }

        $values = implode(', ', $values);
   
        $query = "INSERT into listings ({$fields}) VALUES ({$values})";

        $this->db->query($query, $newListingData);

        redirect('/listings');
      }
    }

    /**
     * Delete a listing
     * 
     * @param array $params
     * @return void
     */

     function destroy($params) {
            $id = $params['id'];

            $params = [
                'id' => $id
            ];

            $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

            if(!$listing) {
                ErrorController::notFound('Listing not found');
                return;
            }

        $this->db->query('DELETE from listings WHERE id = :id', $params);

        // Set flash message
        $_SESSION['success_message'] = 'Listing deleted successfully';

        redirect('/listings');
     }

     /**
      * Show the listing edit form
      */

      public function edit($params) {
        $id = $params['id'] ?? '';

            $params = [
                'id' => $id
            ];

            $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params )->fetch();

            if(!$listing) {
                ErrorController::notFound('Listing not found');
                return;
            }

          

            loadView('listings/edit', [
                'listing' => $listing
            ]);
                }


      /**
      * Upadae a listing
      *
      * @params array $params
      * @return void
      */

      public function update($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params )->fetch();

        if(!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $updatedValues = [];

        $updatedValues = array_intersect_key($_POST, array_flip($allowedFields));

        $updatedValues = array_map('sanitize', $updatedValues);

        $requiredFields = ['title', 'description', 'email', 'city', 'state'];

        $errors = [];

        foreach($requiredFields as $field) {
            if(empty($updatedValues[$field]) || !Validation::string($updatedValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if(!empty($errors)) {
            loadView('listings/edit', [
                'listing' => $listing,
                'errors' => $errors
            ]);
            exit;
        } else {
            // Submit to database
            $updateFields = [];

            foreach(array_keys($updatedValues) as $field) {
                $updateFields[] = "{$field} = :{$field}";
            }

         $updateFields = implode(', ', $updateFields);
         
         $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";

         $updatedValues['id'] = $id;

         $this->db->query($updateQuery, $updatedValues);

         $_SESSION['success_messagte'] = 'Listing updated';

         redirect('/listings/' . $id);
        }
      
       
      }
      
      
  }