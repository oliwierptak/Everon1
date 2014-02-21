<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Resource;

use Everon\Dependency;
use Everon\Exception;
use Everon\Interfaces\Collection;
use Everon\Rest\Interfaces;

class Manager implements Interfaces\ResourceManager
{
    use Dependency\Injection\Factory;
    use Dependency\Injection\DomainManager;
    
    const RANDSALT = '#$@#$#FSFSDF22edfa';
    
    
    /**
     * @var Collection
     */
    protected $ResourceCollection = null;
    
    protected $versions = ['v1'];
    
    protected $current_version = 'v1';
    
    protected $url = '/v1/api/';
    
    
    public function getResource($resource_id, $name)
    {
        //$this->getFactory()->buildRestResource($name, $this->current_version, $data);
        //entity = User
        //resource = Users
        //cut off plural
        $id = $this->generateEntityId($resource_id, $name);
        $name = substr($name, 0, strlen($name) - 1);
        $Repository = $this->getDomainManager()->getRepository($name);
        $data = $Repository->fetchEntityById($id);
        $this->getDomainManager()->getEntity($name, $id, $data);
        
    }
    
    public function generateEntityId($resource_id, $name)
    {
        function decrypt($encrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') {
            // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
            $key = hash('SHA256', $salt . $password, true);
            // Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
            $iv = base64_decode(substr($encrypted, 0, 22) . '==');
            // Remove $iv from $encrypted.
            $encrypted = substr($encrypted, 22);
            // Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
            $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
            // Retrieve $hash which is the last 32 characters of $decrypted.
            $hash = substr($decrypted, -32);
            // Remove the last 32 characters from $decrypted.
            $decrypted = substr($decrypted, 0, -32);
            // Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
            if (md5($decrypted) != $hash) {
                throw new \Exception('shit');
            }
            return $decrypted;
        }
        return decrypt($resource_id, $name, self::RANDSALT);
    }
    
    public function generateResourceId($entity_id, $name)
    {
        function encrypt($decrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') {
            // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
            $key = hash('SHA256', $salt . $password, true);
            // Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
            srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
            if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) {
                throw new \Exception('bullshit');
            }
            // Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
            $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
            return $iv_base64 . $encrypted;
        }

        return encrypt($entity_id, $name, self::RANDSALT);
    }
    
}