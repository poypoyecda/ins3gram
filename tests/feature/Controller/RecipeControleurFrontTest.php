<?php
namespace Tests\Feature\Controller;

use App\Models\RecipeModel;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

final class RecipeControleurFrontTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = true;
    protected $refresh = true;

    protected $DBGroup = 'tests';
    protected $namespace = 'App';
    protected $seed = 'Tests\Support\Database\Seeds\RecipeTestSeeder';
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
      $this->testUserId = 1;

    }

    public function testChargementPage()
    {
    $result= $this->get('/recette');

    $result->assertStatus(200);
    $result->assertSee('ins3gram');
    $result->assertSee('rangeOpinionValue');

    }
    public function testCreerRecette()
    {
        $RecipeModel = new RecipeModel();
        $id =$RecipeModel->insert([
            'name'=> 'Palémo',
            'slug' =>'palemo',
            'alcool' => 1,
            'id_user' => $this->testUserId,
            'description' => 'ceci est une description',
        ]);
        $this->assertIsInt($id);
        $this->seeInDatabase('recipe', [
            'id' => $id,
            'name' => 'Palémo',
            'slug' => 'palmo',
            'alcool' => 1,
            'description' => 'ceci est une description',
        ]);
    }
}