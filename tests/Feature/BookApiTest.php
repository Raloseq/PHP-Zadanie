<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_list_books()
    {
        $this->json('get', 'api/books')
             ->assertStatus(200)
             ->assertJsonStructure(
                [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'created_at',
                        'updated_at'
                    ]
                ]
             );
    }

    public function test_can_create_book()
    {
        $data = [
            'title' => $this->faker->text(5),
            'author' => $this->faker->name()
        ];

        $this->json('post', 'api/books', $data)
             ->assertStatus(201)
             ->assertJsonStructure(
                 [
                     'title',
                     'author',
                     'created_at',
                     'updated_at'
                 ]
             );

        $this->assertDatabaseHas('books',$data);


    }

    public function test_can_create_book_when_title_is_invalid()
    {
        $this->withoutExceptionHandling();

        $data = [
            'title' => $this->faker->text(100),
            'author' => $this->faker->name()
        ];

        $this->json('post', 'api/books', $data)
            ->assertStatus(422);

    }

    public function test_can_create_book_when_author_is_invalid()
    {
        $this->withoutExceptionHandling();

        $data = [
            'title' => $this->faker->text(5),
            'author' => $this->faker->text(100)
        ];

        $this->json('post', 'api/books', $data)
            ->assertStatus(422);

    }

    public function test_show_single_book()
    {
        $book = Book::create(
            [
                'title' => $this->faker->text(5),
                'author' => $this->faker->name()
            ]
        );

        $this->json('get', "api/books/$book->id")
             ->assertStatus(200)
             ->assertExactJson(
                 [
                     'id' => $book->id,
                     'title' => $book->title,
                     'author' => $book->author,
                     'created_at' => $book->created_at,
                     'updated_at' => $book->updated_at
                 ]
             );
    }

    public function test_show_missing_single_book()
    {
        $this->json('get', 'api/books/150')
             ->assertStatus(404);
    }

    public function test_can_update_book()
    {
        $book = Book::create(
            [
                'title' => $this->faker->text(5),
                'author' => $this->faker->name()
            ]
        );

        $data = [
            'title' => $this->faker->text(5),
            'author' => $this->faker->name()
        ];

        $this->json('put', "api/books/$book->id", $data)
             ->assertStatus(200)
             ->assertExactJson(
                 [
                    'id' => $book->id,
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'created_at' => $book->created_at,
                    'updated_at' => $book->updated_at
                 ]
             );
    }

    public function test_can_delete_book()
    {
        $data = [
            'title' => $this->faker->text(5),
            'author' => $this->faker->name()
        ];

        $book = Book::create($data);

        $this->json('delete', "api/books/$book->id")
             ->assertStatus(200);

        $this->assertDatabaseMissing('books',$data);
    }

}
