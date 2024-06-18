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

    // check if listing exists
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
    // restrict field names that can be submitted
    $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'email', 'address', 'city', 'state', 'phone', 'requirements', 'benefits'];

    // check if keys of $_POST array match flipped values from allowed fields array and return only the keys that match
    $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

    $newListingData['user_id'] = 1;

    $newListingData = array_map('sanitize', $newListingData);

    $requiredFields = ['title', 'description', 'salary', 'city', 'state', 'email'];
    $errors = [];

    foreach ($requiredFields as $field) {
      if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
        $errors[$field] = ucfirst($field) . ' is required.';
      }
    }

    if (!empty($errors)) {
      // prevent form submission and reload view with errors
      loadView(
        'listings/create',
        [
          'errors' => $errors,
          'listing' => $newListingData
        ]
      );
    } else {
      // submit data

      // $this->db->query('INSERT INTO listings (title, description, salary, tags, company, address, city, state, phone, email, requirements, benefits, user_id) VALUES(:title, :description, :salary, :tags, :company, :address, :city, :state, :phone, :email, :requirements, :benefits, :user_id)', $newListingData);

      $fields = [];
      $values = [];

      foreach ($newListingData as $field => $value) {
        $fields[] = $field;
      }

      foreach ($newListingData as $field => $value) {
        // convert empty strings to null
        if ($value = '') {
          $newListingData[$field] = null;
        }
        $values[] = ":{$field}";
      }

      $fields = implode(', ', $fields);
      $values = implode(', ', $values);

      // inspect($fields);
      // inspectAndDie($values);
      $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
      $this->db->query($query, $newListingData);

      redirect('/listings');
    }
  }
}
