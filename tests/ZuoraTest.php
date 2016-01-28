<?php

use Spira\ZuoraSdk\Zuora;
use Spira\ZuoraSdk\QueryBuilder;

class ZuoraTest extends TestCase
{
    public function testGetAll()
    {
        $api = $this->makeApi();
        $api->shouldReceive('query')
            ->with('SELECT Id, Name FROM Product WHERE id = 123', 10)
            ->once();

        $zuora = new Zuora($api);
        $zuora->getAll('Product', ['Id', 'Name'], 10, $this->getWhereLambda());
    }

    public function testGetOne()
    {
        $api = $this->makeApi();
        $api->shouldReceive('query')
            ->with('SELECT Id, Name FROM Product WHERE id = 123', 1)
            ->once();

        $zuora = new Zuora($api);
        $zuora->getOne('Product', ['Id', 'Name'], $this->getWhereLambda());
    }

    public function testGetById()
    {
        $api = $this->makeApi();
        $api->shouldReceive('query')
            ->with('SELECT Id, Name FROM Product WHERE id = 321', 1)
            ->once();

        $zuora = new Zuora($api);
        $zuora->getOneById('Product', ['Id', 'Name'], 321);
    }

    /**
     * @expectedException \Spira\ZuoraSdk\Exception\LogicException
     * @expectedExceptionMessage Cannot get ID from array: you should pass string or DataObject
     */
    public function testGetBadId()
    {
        $this->makeZuora(false)->getAllProductRatePlans([123]);
    }

    /**
     * You should have at least one product in your demo account for passing this test.
     *
     * @group integration
     */
    public function testGetAllReturnsArrayOfProductsForOneLimited()
    {
        $api = $this->makeApi(true);
        $zuora = new Zuora($api);
        $columns = ['Id', 'Name'];
        $products = $zuora->getAll('Product', $columns, 1);

        $this->assertTrue(is_array($products));
        $this->assertCount(1, $products);
        $this->checkProductObject(current($products), $columns);
    }

    /**
     * You should have at least one product in your demo account for passing this test.
     *
     * @group integration
     */
    public function testGetAllReturnsArrayOfProducts()
    {
        $api = $this->makeApi(true);
        $zuora = new Zuora($api);
        $columns = ['Id', 'Name'];
        $products = $zuora->getAll('Product', $columns);
        $this->assertTrue(is_array($products));
        $this->assertGreaterThanOrEqual(1, count($products));
        $this->checkProductObject(current($products), $columns);
    }

    /**
     * You should have at least one product in your demo account for passing this test.
     *
     * @group integration
     */
    public function testGetOneReturnsProduct()
    {
        $api = $this->makeApi(true);
        $zuora = new Zuora($api);

        $columns = ['Id', 'Name'];
        $product = $zuora->getOne('Product', $columns);

        $this->checkProductObject($product, $columns);
    }

    protected function getWhereLambda()
    {
        return function (QueryBuilder $builder) {
            $builder->where('id', '=', 123);
        };
    }
}
