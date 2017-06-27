<?php

namespace Bu\ExtraParamConverterBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;
use Bu\ExtraParamConverterBundle\Configuration\ExtraParamConverter;

/**
 * GetConverter automatically converts GET params to entities if they
 * are defined in action method parameters.
 *
 * @author - Lebedinsky Vladimir <Fludimir@gmail.com>
 */
class GetConverter implements ParamConverterInterface
{
    protected $_em;

    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $id = $request->query->get($configuration->getName());
        $class = $configuration->getClass();

        if ($entity = $this->_em->getRepository($class)->find($id)) {
            $request->attributes->set($configuration->getName(), $entity);
        } else {
            throw new NotFoundHttpException("Can't find $class entity with id `$id`.");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ParamConverter $configuration)
    {
        // GetConverter should not override PostConverter - temp dirty fix for that
        return ($configuration instanceof ParamConverter) && !($configuration instanceof ExtraParamConverter);
    }

}
