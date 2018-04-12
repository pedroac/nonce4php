<?php
/**
 * The NoncesManager class.
 */

namespace pedroac\nonce;
use pedroac\nonce\Random;
use Psr\SimpleCache\CacheInterface;
use Kdyby\DateTimeProvider\DateTimeProviderInterface;

/**
 * Nonces manager.
 * 
 * The manager generates and stores nonces using:
 * - a cache provider;
 * - a random string generator;
 * - an expiration interval (eg: one hour);
 * 
 * The server should generate a nonce using a name that specifies a context,
 * for instance, a form that might be filled by some user.
 * The client should get, at least, the nonce value.
 * 
 * When the client requests an action that requires a nonce validation, the
 * server should check if the name and value provided by the client are correct.
 * 
 * The nonce should be expired immediatly after the validation .
 * 
 * @example php\examples\manager.php
 * 
 * @author Pedro Amaral Couto
 * @license MIT
 */
class NoncesManager
{
    /**
     * The random nonce value generator.
     *
     * @var Random
     */
    private $random;

    /**
     * The nonces cache storage.
     *
     * @var CacheInterface
     */
    private $cache;

    /**
     * The nonces expiration interval.
     *
     * @var \DateInterval
     */
    private $expirationInterval;

    /**
     * DateTime provider that should be used as the current date and time.
     *
     * @var DateTimeProviderInterface
     */
    private $now;

    /**
     * Create a nonces manager.
     * 
     * All the constructor arguments should be used to create nonces.  
     * The nonce cache storage ($cache) stores the nodes until they're expired.  
     * The cache values should not be modified outside the manager.  
     * The random generator ($random) generates random nodes values.  
     * The expiration interval ($expirationInterval) is used to calculate the
     * nonces expiration date and time.  
     * The default nonces expiration interval should be one hour.  
     * It's possible to set a DateTime provider to override the clock system,
     * providing DateTime instances that should be used as the current date and
     * time. It's used for unit tests.
     * 
     * @param CacheInterface $cache The nonces cache storage.
     * @param Random $random The random nonces values generator. If NULL, a 
     *  default generator should be used.
     * @param \DateInterval $expirationInterval The nonces expiration interval.
     * @param DateTimeProviderInterface $now DateTime provider that should 
     *  be used as the current date and time.
     */
    public function __construct(
        CacheInterface $cache,
        Random $random = null,
        \DateInterval $expirationInterval = null,
        DateTimeProviderInterface $now = null
    ) {
        if (!$expirationInterval) {
            $expirationInterval = new \DateInterval('PT1H');
        }
        if (!$random) {
            $random = new \pedroac\nonce\Random\HexRandomizer(12);
        }
        $this->random = $random;
        $this->cache = $cache;
        $this->expirationInterval = $expirationInterval;
        $this->now = $now ?? 
            new \Kdyby\DateTimeProvider\Provider\CurrentProvider;
    }

    /**
     * Create a nonce.
     * 
     * - The nonce should be randomly generated and temporarily stored.  
     * - If `$expirationInterval` is not NULL, the manager expiration
     * interval should be ignored.
     * - The returned nonce has the value that should be used by the client.
     * - If a name is not specified a unique name should be generated and the
     * returned nonce should be used to get it.
     *
     * @param string $name The name that can be used to identify the nonce.
     *  If NULL, a name will be generated.
     * @param \DateInterval $expirationInterval The nonce expiration interval that 
     *  should override the default or specified manager expiration interval.
     * @return Nonce The created nonce.
     */
    public function create(
        string $name = null,
        \DateInterval $expirationInterval = null
    ): Nonce {
        if ($name === null) {
            $name = 'token_' . uniqid();
        }
        if (!$expirationInterval) {
            $expirationInterval = $this->expirationInterval;
        }
        $now = $this->now->getDateTime();
        $nonce = new Nonce(
            $name,
            $this->random->randomize(),
            $now->add($expirationInterval),
            $this->now
        );
        $this->cache->set(
            $nonce->getName(),
            $nonce,
            $expirationInterval
        );
        return $nonce;
    }

    /**
     * Verify a token and remove the nonce with the specified name.
     * 
     * Check if there's a nounce which has not expired with the specified 
     * name and value. Also, if there's a nonce with the specified name, it 
     * should be removed from the cache storage.
     * 
     * @param string $name The nonce name.
     * @param string $value The nonce value.
     * @return boolean Is there a valid nonce with the specified name and value?
     * @throws \RuntimeException if the cached value type is unexpected.
     */
    public function verifyAndExpire(
        string $name,
        string $value
    ): bool {
        $isValid = $this->verify($name, $value);
        $this->expire($name);
        return $isValid;
    }

    /**
     * Check if there's a nounce which has not expired with the specified 
     * name and value.
     * 
     * @see \pedroac\nonce\Nonce::verify
     *
     * @param string $name The nonce name.
     * @param string $value The nonce value.
     * @return boolean Is there a valid nonce with the specified name and value?
     * @throws \RuntimeException if the cached value type is unexpected.
     */
    public function verify(
        string $name,
        string $value
    ): bool {
        $nonce = $this->cache->get($name);
        if (!$nonce) {
            return false;
        }
        if (!is_a($nonce, Nonce::class)) {
            throw new \RuntimeException(
                'Expected a nonce but the cached value is not a nonce.'
            );
        }
        return $nonce->verify($name, $value);
    }

    /**
     * Remove a specified nonce.
     *
     * If there's a nonce with the specified name, it should be removed 
     * from the cache storage.
     * 
     * @param string $name The nonce name that should be removed.
     * @return void
     */
    public function expire(string $name)
    {
        $this->cache->delete($name);
    }
}