<?php


namespace Bravesheep\FlysystemUrlBundle\Encoder;


class EncoderRegistry
{
    /**
     * @var EncoderInterface[]
     */
    private $encoders;

    /**
     * @param string $alias
     * @param EncoderInterface $encoder
     */
    public function addEncoder($alias, EncoderInterface $encoder)
    {
        $this->encoders[$alias] = $encoder;
    }

    /**
     * @return EncoderInterface[]
     */
    public function getEncoders()
    {
        return $this->encoders;
    }

    /**
     * @param string $alias
     * @return EncoderInterface
     */
    public function getEncoder($alias)
    {
        return $this->encoders[$alias];
    }

    /**
     * @param string $alias
     * @return bool
     */
    public function hasEncoder($alias)
    {
        return isset($this->encoders[$alias]);
    }
}
