<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class ProductClassControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRoutingAdminProductProductClassNew()
    {
        $this->client->request('GET',
            $this->app->url('admin_product_product_class_new')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductProductClassEdit()
    {
        // before
        $TestCreator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestProduct = $this->newTestProduct($TestCreator);
        $this->app['orm.em']->persist($TestProduct);
        $this->app['orm.em']->flush();
        $TestClassName = $this->newTestClassName($TestCreator);
        $this->app['orm.em']->persist($TestClassName);
        $this->app['orm.em']->flush();
        $TestClassCategory1 = $this->newTestClassCategory($TestCreator, $TestClassName);
        $this->app['orm.em']->persist($TestClassCategory1);
        $this->app['orm.em']->flush();
        $TestClassCategory2 = $this->newTestClassCategory($TestCreator, $TestClassName);
        $this->app['orm.em']->persist($TestClassCategory2);
        $this->app['orm.em']->flush();
        $TestProductClass = $this->newTestProductClass($TestCreator, $TestProduct, $TestClassCategory1, $TestClassCategory2);
        $this->app['orm.em']->persist($TestProductClass);
        $this->app['orm.em']->flush();
        $test_product_class = $this->app['eccube.repository.product_class']
            ->findOneBy(array(
                'product_code' => $TestProductClass->setProductCode()
            ))
            ->getId();

        // main
        $this->client->request('GET',
            $this->app->url('admin_product_product_class_edit', array('id' => $test_product_class))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // after
        $this->app['orm.em']->remove($TestProductClass);
        $this->app['orm.em']->flush();
        $this->app['orm.em']->remove($TestClassCategory2);
        $this->app['orm.em']->flush();
        $this->app['orm.em']->remove($TestClassCategory1);
        $this->app['orm.em']->flush();
        $this->app['orm.em']->remove($TestClassName);
        $this->app['orm.em']->flush();
        $this->app['orm.em']->remove($TestProduct);
        $this->app['orm.em']->flush();

    }

    private function newTestProduct($TestCreator)
    {
        $TestProduct = new \Eccube\Entity\Product();
        $TestProduct->setName('テスト商品')
            ->setStatus(0)
            ->setNote('test note')
            ->setDescriptionList('テスト商品 商品説明(リスト)')
            ->setDescriptionDetail('テスト商品 商品説明(詳細)')
            ->setFreeArea('フリー記載')
            ->setDelFlg(0)
            ->setCreator($TestCreator);

        return $TestProduct;
    }

    private function newTestClassName($TestCreator)
    {
        $Creator = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Member')
            ->find(1);
        $TestClassName = new \Eccube\Entity\ClassName();
        $TestClassName->setName('形状')
            ->setRank(100)
            ->setDelFlg(0)
            ->setCreator($TestCreator);

        return $TestClassName;
    }

    private function newTestClassCategory($TestCreator, $TestClassName)
    {
        $TestClassCategory = new \Eccube\Entity\ClassCategory();
        $TestClassCategory->setName('立方体')
            ->setRank(100)
            ->setClassName($TestClassName)
            ->setDelFlg(0)
            ->setCreator($TestCreator);

        return $TestClassCategory;
    }

    private function newTestProductClass($TestCreator, $TestProduct, $TestClassCategory1, $TestClassCategory2)
    {
        $TestClassCategory = new \Eccube\Entity\ProductClass();
        $TestClassCategory->setProduct($TestProduct)
            ->setClassCategory1($TestClassCategory1)
            ->setClassCategory2($TestClassCategory2)
            ->setProductType(1)
//            ->setProductCode('test code')
            ->setStock(100)
            ->setStockUnlimited(0)
//            ->setDeliveryDateId(1)
            ->setSaleLimit(10)
            ->setPrice01(10000)
            ->setPrice02(5000)
            ->setDelivFee(1000)
            ->setPointRate(5)
            ->setCreator($TestCreator)
            ->setDelFlg(0);
        return $TestClassCategory;
    }
}