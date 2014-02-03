<?php
namespace Everon\Domain\User;

use Everon\Domain;
use Everon\Helper;
use Everon\Dependency;
use Everon\Domain\Exception;

/**
 * @method \Everon\DataMapper\Interfaces\User getMapper()
 */
class Repository extends Domain\Repository
{
    use Dependency\Injection\DomainManager;
    

    /**'
     * @param $username
     * @return mixed
     * @throws Exception\Repository
     */
    public function getByLogin($username)
    {
        $Criteria = new \Everon\DataMapper\Criteria([
            'login' => $username,
            'first_name' => 'John'
        ]);

        $data = $this->getMapper()->fetchAll($Criteria);
        if (empty($data)) {
            throw new Exception\Repository('Login failed for: "%s"', $username);
        }

        $data = current($data);
        $pk_name = $this->getMapper()->getSchemaTable()->getPk();
        $id = $data[$pk_name];
        $id = $this->getMapper()->getSchemaTable()->validateId($id);

        return $this->getDomainManager()->getEntity($this->getName(), $id, $data);
    }
}
