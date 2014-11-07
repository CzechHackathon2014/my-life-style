<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\Model;

use Nette,
	Nette\Database\Context,
	Aprila\DuplicateEntryException,
	Nette\Utils\Image;


class ReferenceManager extends Nette\Object
{

	/**
	 * @var \Nette\Database\Context
	 */
	protected $database;

	/** @var  string */
	protected $dir;

	/** @var  string */
	protected $uri;

	/** @var  bool */
	protected $useFiles = FALSE;


	/**
	 * @param \Nette\Database\Context $database
	 */
	public function __construct(Context $database)
	{
		$this->database = $database;
	}


	public function setFilesFolder($dir, $uri)
	{
		$this->dir = $dir;
		$this->uri = $uri;
		if (is_dir($this->dir)) {
			$this->useFiles = TRUE;
		}
	}


	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function table()
	{
		return $this->database->table('reference');
	}


	/********************* find* methods & get *********************/


	/**
	 * @param int $id
	 * @return Nette\Database\Table\IRow
	 */
	public function get($id)
	{
		return $this->table()->get($id);
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->table()->count();
	}


	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->table();
	}


	/**
	 * return random references without image
	 *
	 * @param int $count
	 * @return array|Nette\Database\Table\IRow[]
	 */
	public function findRandom($count = 3)
	{
		return $this->table()->where("onlyLogo = '0'")->order("rand()")->limit($count)->fetchAll();
	}


	/**
	 * return random references with image
	 *
	 * @param int $count
	 * @return array|Nette\Database\Table\IRow[]
	 */
	public function findRandomWithImage($count = 4)
	{
		return $this->table()->where("onlyLogo = '0'")->where("image != ''")->order("rand()")->limit($count)->fetchAll();
	}


	/**
	 * return random references without image and minimal stars
	 *
	 * @param int $count
	 * @param int $minimumStars
	 * @return array|Nette\Database\Table\IRow[]
	 */
	public function findRandomWithStars($count = 3, $minimumStars = 3)
	{
		return $this->table()->where("onlyLogo = '0'")->where("stars > ?", $minimumStars)->order("rand()")->limit($count)->fetchAll();
	}


	/**
	 * return random logos references
	 *
	 * @param int $count
	 * @return array|Nette\Database\Table\IRow[]
	 */
	public function findRandomLogos($count = 3)
	{
		return $this->table()->where("onlyLogo = '1'")->order("rand()")->limit($count)->fetchAll();
	}


	/**
	 * @param $query
	 * @return \Nette\Database\Table\Selection
	 */
	public function findFulltext($query)
	{
		return $this->table()
			->where('name LIKE ? OR company LIKE ? OR content LIKE ?',
				array(
					"%" . $query . "%",
					"%" . $query . "%",
					"%" . $query . "%")
			);
	}


	/********************* update* methods *********************/


	/**
	 * resize image and save to folder
	 *
	 * @param Nette\Http\FileUpload $image
	 * @return string
	 * @throws \Nette\Utils\UnknownImageFileException
	 */
	public function saveImage(Nette\Http\FileUpload $image)
	{
		$filename = $image->getSanitizedName();
		$path = $this->dir;

		$filename = $this->getFileName($path, $filename);

		$image->move("$path/$filename");
		$filePath = "{$this->uri}/$filename";

		$image = Image::fromFile("$path/$filename");
		$image->resize(150, 150, Image::EXACT);

		$image->save("$path/$filename");

		return $filePath;
	}


	/**
	 * return unique name for new file
	 *
	 * @param $path
	 * @param $filename
	 * @return string
	 */
	public function getFileName($path, $filename)
	{
		if (file_exists("$path/$filename")) {
			$filename = Nette\Utils\Random::generate() . '_' . $filename;
			$filename = $this->getFileName($path, $filename);
		} else {
			$filename;
		}

		return $filename;
	}


	/**
	 * add reference
	 *
	 * @param bool $onlyLogo
	 * @param string $name
	 * @param string $company
	 * @param string $content
	 * @param int $stars
	 * @param string $image
	 * @return IRow
	 */
	public function addReference($onlyLogo, $name, $company, $content, $stars = 0, Nette\Http\FileUpload $image = NULL)
	{
		$filePath = '';

		if ($this->useFiles && $image && $image->isOk()) {
			$filePath = $this->saveImage($image);
		}

		$data = array(
			'onlyLogo' => $onlyLogo,
			'name' => $name,
			'company' => $company,
			'content' => $content,
			'stars' => $stars,
			'image' => $filePath,
		);

		try {
			$newRow = $this->table()->insert($data);

		} catch (\PDOException $e) {
			if ($e->getCode() == '23000') {
				throw new DuplicateEntryException;
			} else {
				throw $e;
			}
		}

		return $newRow;
	}


	/**
	 * edit reference
	 *
	 * @param $id
	 * @param $onlyLogo
	 * @param $name
	 * @param $company
	 * @param $content
	 * @param int $stars
	 * @param Nette\Http\FileUpload $image
	 * @return bool
	 */
	public function editReference($id, $onlyLogo, $name, $company, $content, $stars = 0, Nette\Http\FileUpload $image = NULL)
	{
		$data = array(
			'name' => $name,
			'onlyLogo' => $onlyLogo,
			'company' => $company,
			'content' => $content,
			'stars' => $stars,
		);

		if ($this->useFiles && $image && $image->isOk()) {
			$data['image'] = $this->saveImage($image);
		}

		try {
			$status = $this->get($id)->update($data);

		} catch (\PDOException $e) {
			if ($e->getCode() == '23000') {
				throw new DuplicateEntryException;
			} else {
				throw $e;
			}
		}

		return (bool)$status;
	}


	/**
	 * delete reference
	 *
	 * @param $id int
	 * @return mixed
	 */
	public function deleteReference($id)
	{
		return $this->get($id)->delete();
	}


	/**
	 * edit object
	 * todo: to parent object
	 * todo: create add($data)
	 *
	 * @param $id
	 * @param $data
	 * @return bool
	 */
	public function edit($id, $data)
	{
		try {
			$status = $this->get($id)->update($data);

		} catch (\PDOException $e) {
			if ($e->getCode() == '23000') {
				throw new DuplicateEntryException;
			} else {
				throw $e;
			}
		}

		return (bool)$status;
	}
}