<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\HttpKernel\Fragment;

use ECSPrefix20210508\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210508\Symfony\Component\HttpKernel\Controller\ControllerReference;
use ECSPrefix20210508\Symfony\Component\HttpKernel\EventListener\FragmentListener;
/**
 * Adds the possibility to generate a fragment URI for a given Controller.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class RoutableFragmentRenderer implements \ECSPrefix20210508\Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface
{
    private $fragmentPath = '/_fragment';
    /**
     * Sets the fragment path that triggers the fragment listener.
     *
     * @see FragmentListener
     * @param string $path
     */
    public function setFragmentPath($path)
    {
        if (\is_object($path)) {
            $path = (string) $path;
        }
        $this->fragmentPath = $path;
    }
    /**
     * Generates a fragment URI for a given controller.
     *
     * @param bool $absolute Whether to generate an absolute URL or not
     * @param bool $strict   Whether to allow non-scalar attributes or not
     *
     * @return string A fragment URI
     */
    protected function generateFragmentUri(\ECSPrefix20210508\Symfony\Component\HttpKernel\Controller\ControllerReference $reference, \ECSPrefix20210508\Symfony\Component\HttpFoundation\Request $request, $absolute = \false, $strict = \true)
    {
        if ($strict) {
            $this->checkNonScalar($reference->attributes);
        }
        // We need to forward the current _format and _locale values as we don't have
        // a proper routing pattern to do the job for us.
        // This makes things inconsistent if you switch from rendering a controller
        // to rendering a route if the route pattern does not contain the special
        // _format and _locale placeholders.
        if (!isset($reference->attributes['_format'])) {
            $reference->attributes['_format'] = $request->getRequestFormat();
        }
        if (!isset($reference->attributes['_locale'])) {
            $reference->attributes['_locale'] = $request->getLocale();
        }
        $reference->attributes['_controller'] = $reference->controller;
        $reference->query['_path'] = \http_build_query($reference->attributes, '', '&');
        $path = $this->fragmentPath . '?' . \http_build_query($reference->query, '', '&');
        if ($absolute) {
            return $request->getUriForPath($path);
        }
        return $request->getBaseUrl() . $path;
    }
    private function checkNonScalar(array $values)
    {
        foreach ($values as $key => $value) {
            if (\is_array($value)) {
                $this->checkNonScalar($value);
            } elseif (!\is_scalar($value) && null !== $value) {
                throw new \LogicException(\sprintf('Controller attributes cannot contain non-scalar/non-null values (value for key "%s" is not a scalar or null).', $key));
            }
        }
    }
}