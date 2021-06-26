<?php

namespace OCA\Biblio\Tests\Unit\Service;

use OCA\Biblio\Service\MediumNotFound;
use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Db\DoesNotExistException;

use OCA\Biblio\Db\Medium;
use OCA\Biblio\Service\MediumService;
use OCA\Biblio\Db\MediumMapper;

class MediumServiceTest extends TestCase {
	private $service;
	private $mapper;
	private $userId = 'john';

	public function setUp(): void {
		$this->mapper = $this->getMockBuilder(MediumMapper::class)
			->disableOriginalConstructor()
			->getMock();
		$this->service = new MediumService($this->mapper);
	}

	public function testUpdate() {
		// the existing medium
		$medium = Medium::fromRow([
			'id' => 3,
			'title' => 'yo',
			'fields' => 'nope'
		]);
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(3))
			->will($this->returnValue($medium));

		// the medium when updated
		$updatedMedium = Medium::fromRow(['id' => 3]);
		$updatedMedium->setTitle('title');
		$updatedMedium->setFields('fields');
		$this->mapper->expects($this->once())
			->method('update')
			->with($this->equalTo($updatedMedium))
			->will($this->returnValue($updatedMedium));

		$result = $this->service->update(3, 'title', 'fields', $this->userId);

		$this->assertEquals($updatedMedium, $result);
	}

	public function testUpdateNotFound() {
		$this->expectException(MediumNotFound::class);
		// test the correct status code if no medium is found
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(3))
			->will($this->throwException(new DoesNotExistException('')));

		$this->service->update(3, 'title', 'fields', $this->userId);
	}
}
